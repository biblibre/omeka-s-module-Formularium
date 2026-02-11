<?php
namespace Formularium\Form;

use Laminas\Form\Form;

class FormSubmissionBatchUpdateForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'o:handled',
            'type' => 'Laminas\Form\Element\Select',
            'options' => [
                'label' => 'Handled', // @translate
                'value_options' => [
                    '' => '[No change]', // @translate
                    '1' => 'Mark as handled', // @translate
                    '0' => 'Mark as not handled', // @translate
                ],
            ],
            'attributes' => [
                'value' => '',
            ],
        ]);
    }
}
