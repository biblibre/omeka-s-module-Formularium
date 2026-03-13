<?php

namespace Formularium\FormComponentType;

use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;

abstract class AbstractInput extends AbstractFormComponentType
{
    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        $fieldset->add([
            'name' => 'name',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'HTML element name', // @translate
                'info' => "Value of the HTML element's name attribute. It should be unique. This name can then be used to reference this component in form actions. It must contain only alphanumeric characters and underscores.", // @translate
            ],
            'attributes' => [
                'required' => true,
                'pattern' => '[A-Za-z0-9_]+',
                'title' => 'The name must contain only alphanumeric characters (A-Z, a-z, 0-9) and underscores (_)', // @title
            ],
        ]);

        $fieldset->add([
            'name' => 'label',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Label', // @translate
            ],
        ]);

        $fieldset->add([
            'name' => 'info',
            'type' => 'Laminas\Form\Element\Textarea',
            'options' => [
                'label' => 'Help text', // @translate
            ],
        ]);

        $fieldset->add([
            'name' => 'hide_info',
            'type' => 'Laminas\Form\Element\Checkbox',
            'options' => [
                'label' => 'Hide help text by default', // @translate
            ],
        ]);

        $fieldset->add([
            'name' => 'required',
            'type' => 'Laminas\Form\Element\Checkbox',
            'options' => [
                'label' => 'Required', // @translate
            ],
        ]);
    }

    protected function getFormElementSpec(FormComponent $formComponent): array
    {
        $label = trim($formComponent->getSetting('label', ''));

        return [
            'name' => $formComponent->getSetting('name'),
            'options' => [
                'label' => $label !== '' ? $label : null,
                'info' => $formComponent->getSetting('info'),
                'hide_info' => $formComponent->getSetting('hide_info') ? true : false,
            ],
            'attributes' => [
                'required' => $formComponent->getSetting('required') ? true : false,
            ],
        ];
    }

    public function formAddElements(Form $form, FormComponent $formComponent): void
    {
        $form->add($this->getFormElementSpec($formComponent));
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

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        $name = $formComponent->getSetting('name');
        $element = $form->get($name);
        if (isset($data[$name])) {
            $element->setValue($data[$name]);
        }

        return $renderer->formRow($element, null, null, 'formularium/common/form-row');
    }
}
