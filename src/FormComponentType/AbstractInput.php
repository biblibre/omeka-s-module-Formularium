<?php

namespace Formularium\FormComponentType;

use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\View\Renderer\PhpRenderer;

abstract class AbstractInput extends AbstractFormComponentType
{
    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        $fieldset->add([
            'name' => 'name',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Name', // @translate
            ],
            'attributes' => [
                'required' => true,
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
            'name' => 'required',
            'type' => 'Laminas\Form\Element\Checkbox',
            'options' => [
                'label' => 'Required', // @translate
            ],
        ]);
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        $name = $formComponent->getSetting('name');
        $element = $form->get($name);
        if (isset($data[$name])) {
            $element->setValue($data[$name]);
        }

        return $renderer->formRow($element);
    }
}
