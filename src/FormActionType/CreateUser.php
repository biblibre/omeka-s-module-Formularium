<?php

namespace Formularium\FormActionType;

use Laminas\Form\Fieldset;
use Omeka\Api\Representation\UserRepresentation;
use Formularium\Api\Representation\FormSubmissionRepresentation;
use Formularium\Api\Representation\FormActionResultRepresentation;
use Omeka\Stdlib\Mailer;
use Omeka\Api\Manager;
use Omeka\Module\Manager as ModuleManger;
use Omeka\Permissions\Acl;
use Omeka\Api\Exception\ValidationException;
use Laminas\Mail\Exception\ExceptionInterface as MailException;

class CreateUser extends AbstractFormActionType
{
    public function __construct(
        protected Mailer $mailer,
        protected Manager $api,
        protected Acl $acl,
        protected ModuleManger $moduleManager,
    ) { }

    public function getLabel(): string
    {
        return 'Create a user'; // @translate
    }

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        $fieldset->add([
            'name' => 'email',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'User Email form label', // @translate
                'info' => 'This should corespond to the `HTML element name` field of the form component that will be used for entering the user email.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $fieldset->add([
            'name' => 'name',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'User Name form label', // @translate
                'info' => 'This should corespond to the `HTML element name` field of the form component that will be used for entering the user username.', // @translate
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
                'value_options' => $this->acl->getRoleLabels()
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        if ($this->isGroupModuleActive()) {
            $groupOptions = [];

            foreach($this->api->search('groups')->getContent() as $group) {
                $groupOptions[$group->id()] = $group->name();
            }

            $fieldset->add([
                'name' => 'group',
                // TODO: Make the from be able to use elements initialized by factories.
                'type' => 'Laminas\Form\Element\Select', 
                'options' => [
                    'label' => 'Groups', // @translate
                    'value_options' => $groupOptions,
                    'name_as_value' => true,
                    'resource_value_options' => [
                        'resource' => 'groups',
                        'query' => [],
                        'option_text_callback' => function($v) { return $v->name(); }
                    ],
                ],
                'attributes' => [
                    'multiple' => true,
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Select groups...' // @translate
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

        $reponse;
        try {
            $response = $this->api->create('users', $userRequestData);
        } catch (ValidationException $e) {
            return [
                'o:status' => FormActionResultRepresentation::FAILED,
                'o:data' => [ 
                    'user' => 'creation failed',
                    'activation_mail' => 'not sent', 
                ],
            ];
        }
        $user = $response->getContent()->getEntity();

        try {
            $this->mailer->sendUserActivation($user);
        } catch (MailException $e) {
            return [
                'o:status' => FormActionResultRepresentation::FAILED,
                'o:data' => [ 
                    'user' => 'created',
                    'activation_mail' => 'could not sent', 
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
    function isGroupModuleActive() {
        $groupModule = $this->moduleManager->getModule('Group');
        return $groupModule && $groupModule->getState() === 'active';
    }

}
