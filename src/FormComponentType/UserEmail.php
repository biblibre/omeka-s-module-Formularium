<?php

namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\FormComponent\FormComponent;
use Laminas\Authentication\AuthenticationService;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;

class UserEmail extends AbstractInput
{
    public function __construct(protected AuthenticationService $authenticationService)
    {
    }

    public function getLabel(): string
    {
        return 'User e-mail address'; // @translate
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        return $renderer->partial('formularium/form-component-type/user-email', ['form' => $form, 'formComponent' => $formComponent, 'data' => $data]);
    }

    protected function getFormElementSpec(FormComponent $formComponent): array
    {
        $spec = parent::getFormElementSpec($formComponent);

        $spec['type'] = 'Laminas\Form\Element\Email';

        $user = $this->authenticationService->getIdentity();
        if ($user) {
            $spec['attributes']['value'] = $user->getEmail();
        }

        return $spec;
    }

    public function hydrateFormSubmission(FormComponent $formComponent, Request $request, FormulariumFormSubmission $formSubmission, ErrorStore $errorStore)
    {
        $name = $formComponent->getSetting('name');
        $formData = $request->getValue('form_data');

        if (isset($formData[$name])) {
            $formSubmissionData = $formSubmission->getData();
            $formSubmissionData[$name] = $formData[$name];
            $formSubmission->setData($formSubmissionData);
        }
    }
}
