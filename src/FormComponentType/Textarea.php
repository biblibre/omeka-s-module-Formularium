<?php

namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\FormComponent\FormComponent;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;

class Textarea extends AbstractInput
{
    public function getLabel(): string
    {
        return 'Text area'; // @translate
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        return $renderer->partial('formularium/form-component-type/text-area', ['form' => $form, 'formComponent' => $formComponent, 'data' => $data]);
    }

    protected function getFormElementSpec(FormComponent $formComponent): array
    {
        $spec = parent::getFormElementSpec($formComponent);

        $spec['type'] = 'Laminas\Form\Element\Textarea';

        return $spec;
    }

    public function formAddInputFilters(InputFilterInterface $inputFilter, FormComponent $formComponent): void
    {
        $required = $formComponent->getSetting('required') ? true : false;

        $inputFilter->add([
            'name' => $formComponent->getSetting('name'),
            'required' => $required,
            'allow_empty' => !$required,
            'filters' => [
                ['name' => 'Laminas\Filter\StringTrim'],
            ],
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
