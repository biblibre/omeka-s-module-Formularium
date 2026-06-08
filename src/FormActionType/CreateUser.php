<?php

namespace Formularium\FormActionType;

use Formularium\Api\Representation\FormSubmissionRepresentation;
use Formularium\Api\Representation\FormActionResultRepresentation;
use Laminas\Mail\Exception\ExceptionInterface as MailException;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Form\Fieldset;
use Laminas\Log\Logger;
use Omeka\Stdlib\Mailer;
use Omeka\Api\Manager;
use Omeka\Module\Manager as ModuleManger;
use Omeka\Permissions\Acl;
use Omeka\Api\Exception\ValidationException;

class CreateUser extends AbstractFormActionType
{
    public function __construct(
        protected Mailer $mailer,
        protected Manager $api,
        protected Acl $acl,
        protected ModuleManger $moduleManager,
        protected Logger $logger,
    ) {
    }

    public function getLabel(): string
    {
        return 'Create a user'; // @translate
    }

    public function prepareForm(PhpRenderer $view): void
    {
        $view->headScript()->appendFile($view->assetUrl('js/formularium-form-action-type-createuser.js', 'Formularium'));
    }

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        $fieldset->add([
            'name' => 'email',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'User Email form elment name', // @translate
                'info' => 'This should correspond to the `HTML element name` field of the form component that will be used for entering the user email.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $fieldset->add([
            'name' => 'name',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Username form element name', // @translate
                'info' => 'This should correspond to the `HTML element name` field of the form component that will be used for entering the user username.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $fieldset->add([
            'name' => 'role',
            'type' => 'Laminas\Form\Element\Select',
            'options' => [
                'label' => 'User Role', // @translate
                'info' => 'Role the created user will have.', // @translate
                'value_options' => $this->acl->getRoleLabels(true), // Exclude admin roles.
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        if ($this->isGroupModuleActive()) {
            $groupOptions = [];

            foreach ($this->api->search('groups')->getContent() as $group) {
                $groupOptions[$group->id()] = $group->name();
            }

            $fieldset->add([
                'name' => 'group',
                'type' => 'Laminas\Form\Element\Select',
                'options' => [
                    'label' => 'Groups', // @translate
                    'value_options' => $groupOptions,
                    'name_as_value' => true,
                ],
                'attributes' => [
                    'multiple' => true,
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Select groups...', // @translate
                ],
            ]);
        }
    }

    public function perform(
        array $action,
        FormSubmissionRepresentation $formSubmission,
        array $data,
    ): array {
        $formData = $formSubmission->data();
        $userRequestData = [
            'o:email' => $formData[$action['settings']['email']],
            'o:name' => $formData[$action['settings']['name']],
            'o:role' => $action['settings']['role'],
        ];

        if ($this->isGroupModuleActive()) {
            $userRequestData['o-module-group:group'] = $action['settings']['group'];
        }

        $this->acl->allow(null, 'Omeka\Api\Adapter\UserAdapter', 'create');
        $this->acl->allow(null, 'Omeka\Entity\User', 'create');
        $this->acl->allow(null, 'Omeka\Entity\User', 'change-role');
        try {
            $response = $this->api->create('users', $userRequestData);
        } catch (ValidationException $e) {
            $this->logger->err((string) $e);
            return [
                'o:status' => FormActionResultRepresentation::FAILED,
                'o:data' => [
                    'user' => 'Creation failed',
                    'reason' => $e->getErrorStore()->getErrors(),
                    'activation_mail' => 'Not sent',
                ],
            ];
        }
        $this->acl->removeAllow(null, 'Omeka\Api\Adapter\UserAdapter', 'create');
        $this->acl->removeAllow(null, 'Omeka\Entity\User', 'create');
        $this->acl->removeAllow(null, 'Omeka\Entity\User', 'change-role');
        $user = $response->getContent()->getEntity();

        try {
            $this->mailer->sendUserActivation($user);
        } catch (MailException $e) {
            $this->logger->err((string) $e);
            return [
                'o:status' => FormActionResultRepresentation::FAILED,
                'o:data' => [
                    'user' => 'Created',
                    'activation_mail' => 'Could not sent',
                    'reason' => $e->getMessage(),
                ],
            ];
        }

        return [
            'o:status' => FormActionResultRepresentation::SUCCEEDED,
            'o:data' => [
                'user' => 'created',
                'activation_mail' => 'sent',
            ],
        ];
    }

    // Used to add support for the Group Module.
    private function isGroupModuleActive()
    {
        $groupModule = $this->moduleManager->getModule('Group');
        return $groupModule && $groupModule->getState() === 'active';
    }

}
