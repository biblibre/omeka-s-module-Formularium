<?php

namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;

class Recaptcha extends AbstractFormComponentType
{
    public function getLabel(): string
    {
        return 'Recaptcha (antispam)'; // @translate
    }

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        $fieldset->add([
            'name' => 'label',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Label', // @translate
            ],
        ]);
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        return $renderer->partial('formularium/form-component-type/recaptcha', ['form' => $form, 'formComponent' => $formComponent, 'data' => $data]);
    }

    public function formAddElements(Form $form, FormComponent $formComponent): void
    {
        $form->add([
            'type' => 'Omeka\Form\Element\Recaptcha',
            'options' => [
                'label' => $formComponent->getSetting('label'),
            ],
        ]);
    }
}
