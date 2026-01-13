<?php

namespace Formularium\Form;

use Formularium\FormComponentType\FormComponentTypeInterface;
use Formularium\FormComponentType\FormComponentTypeManager;
use Laminas\Form\Form;

class FormForm extends Form
{
    public function init()
    {
        $this->setAttribute('novalidate', true);

        $this->add([
            'name' => 'o:name',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Name', // @translate
            ],
            'attributes' => [
                'id' => 'name',
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'o:components',
            'type' => 'Formularium\Form\Element\Components',
            'options' => [
                'label' => 'Components', // @translate
            ],
            'attributes' => [
                'id' => 'components',
            ],
        ]);

        $this->add([
            'name' => 'o:actions',
            'type' => 'Formularium\Form\Element\Actions',
            'options' => [
                'label' => 'Actions', // @translate
            ],
            'attributes' => [
                'id' => 'actions',
            ],
        ]);
    }
}
