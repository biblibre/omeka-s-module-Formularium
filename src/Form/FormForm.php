<?php

namespace Formularium\Form;

use Laminas\Form\Form;

class FormForm extends Form
{
    public function init()
    {
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
            'name' => 'o:resource_page_block_title',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Resource page block title', // @translate
                'info' => 'Title used when displaying the form as a resource page block', // @translate
            ],
            'attributes' => [
                'id' => 'resource_page_block_title',
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
