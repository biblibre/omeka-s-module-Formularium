<?php

namespace Formularium\FormComponentType;

use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;

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
}
