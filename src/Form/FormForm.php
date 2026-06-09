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

        $this->add([
            'name' => 'o:settings',
            'type' => 'Laminas\Form\Fieldset',
        ]);
        $settingsFieldset = $this->get('o:settings');

        $settingsFieldset->add([
            'name' => 'submit_button_label',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Submit button label', // @translate
                'info' => 'Label of the button used to submit the form', // @translate
            ],
            'attributes' => [
                'id' => 'submit_button_label',
            ],
        ]);

        $settingsFieldset->add([
            'name' => 'resource_page_block_title',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Resource page block · Title', // @translate
                'info' => 'Title used when displaying the form as a resource page block', // @translate
            ],
            'attributes' => [
                'id' => 'resource_page_block_title',
            ],
        ]);

        $settingsFieldset->add([
            'name' => 'resource_page_block_collapse',
            'type' => 'Laminas\Form\Element\Checkbox',
            'options' => [
                'label' => 'Resource page block · Hide form', // @translate
                'info' => 'If enabled, the resource page block will only display a button which, when clicked, will make the form appear', // @translate
            ],
            'attributes' => [
                'id' => 'resource_page_block_collapse',
            ],
        ]);

        $settingsFieldset->add([
            'name' => 'resource_page_block_collapse_button_label',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Resource page block · Show/hide button label', // @translate
                'info' => 'Label of the button used to show/hide the form', // @translate
            ],
            'attributes' => [
                'id' => 'resource_page_block_collapse_button_label',
            ],
        ]);
    }
}
