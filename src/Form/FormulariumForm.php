<?php

namespace Formularium\Form;

use Laminas\Form\Form;
use Formularium\FormComponentType\FormComponentTypeManager;
use Formularium\FormComponent\FormComponent;

class FormulariumForm extends Form
{
    protected FormComponentTypeManager $formComponentTypeManager;

    public function init()
    {
        $formulariumForm = $this->getOption('formularium_form');

        // The value attribute will be populated by the block layout
        $this->add([
            'name' => 'formularium_site_page_block_id',
            'type' => 'Laminas\Form\Element\Hidden',
        ]);
        $this->add([
            'name' => 'formularium_site_page_id',
            'type' => 'Laminas\Form\Element\Hidden',
        ]);

        foreach ($formulariumForm->components() as $formComponent) {
            $formComponentType = $this->formComponentTypeManager->get($formComponent['type']);
            $formComponentType->formAddElements($this, new FormComponent($formComponent));
        }

        $inputFilter = $this->getInputFilter();
        foreach ($formulariumForm->components() as $formComponent) {
            $formComponentType = $this->formComponentTypeManager->get($formComponent['type']);
            $formComponentType->formAddInputFilters($inputFilter, new FormComponent($formComponent));
        }
    }

    public function setFormComponentTypeManager(FormComponentTypeManager $formComponentTypeManager)
    {
        $this->formComponentTypeManager = $formComponentTypeManager;
    }
}
