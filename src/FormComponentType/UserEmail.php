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

    public function formAddElements(Form $form, FormComponent $formComponent): void
    {
        $user = $this->authenticationService->getIdentity();

        $form->add([
            'type' => 'Laminas\Form\Element\Email',
            'name' => $formComponent->getSetting('name'),
            'options' => [
                'label' => $formComponent->getSetting('label'),
                'info' => $formComponent->getSetting('info'),
            ],
            'attributes' => [
                'required' => $formComponent->getSetting('required') ? true : false,
                'value' => $user ? $user->getEmail() : '',
            ],
        ]);
    }

    public function formAddInputFilters(InputFilterInterface $inputFilter, FormComponent $formComponent): void
    {
        $required = $formComponent->getSetting('required') ? true : false;

        $inputFilter->add([
            'name' => $formComponent->getSetting('name'),
            'required' => $required,
            'allow_empty' => !$required,
        ]);
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
