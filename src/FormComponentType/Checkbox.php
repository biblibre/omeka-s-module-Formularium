<?php

namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\FormComponent\FormComponent;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;

class Checkbox extends AbstractInput
{
    public function getLabel(): string
    {
        return 'Checkbox'; // @translate
    }

    protected function getFormElementSpec(FormComponent $formComponent): array
    {
        $spec = parent::getFormElementSpec($formComponent);

        $spec['type'] = 'Laminas\Form\Element\Checkbox';

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
