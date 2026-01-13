<?php

namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Request;
use Omeka\Api\Manager as ApiManager;
use Omeka\Stdlib\ErrorStore;

class Select extends AbstractInput
{
    public function __construct(protected ApiManager $api)
    {
    }

    public function getLabel(): string
    {
        return 'Dropdown list'; // @translate
    }

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        parent::settingsFieldsetAddElements($fieldset);

        try {
            $vocabs = $this->api->search('custom_vocabs')->getContent();
            $valueOptions = [];
            foreach ($vocabs as $vocab) {
                $valueOptions[$vocab->id()] = $vocab->label();
            }

            $fieldset->add([
                'type' => 'Laminas\Form\Element\Select',
                'name' => 'custom_vocab_id',
                'options' => [
                    'label' => 'Custom Vocab', // @translate
                    'value_options' => $valueOptions,
                    'empty_option' => '',
                ],
            ]);
        } catch (\Throwable $e) {
            // CustomVocab is not enabled, do nothing
        }

        $fieldset->add([
            'type' => 'Laminas\Form\Element\Textarea',
            'name' => 'options',
            'options' => [
                'label' => 'Options', // @translate
                'info' => 'One option per line', // @translate
            ],
        ]);
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        return $renderer->partial('formularium/form-component-type/select', ['form' => $form, 'formComponent' => $formComponent, 'data' => $data]);
    }

    public function formAddElements(Form $form, FormComponent $formComponent): void
    {
        $valueOptions = [];

        $custom_vocab_id = $formComponent->getSetting('custom_vocab_id');
        if ($custom_vocab_id) {
            $vocab = $this->api->read('custom_vocabs', $custom_vocab_id)->getContent();
            $valueOptions = $vocab->listValues();
        } else {
            $options = $formComponent->getSetting('options', '');
            $options = explode("\n", $options);
            $valueOptions = array_combine($options, $options);
        }

        $form->add([
            'type' => 'Laminas\Form\Element\Select',
            'name' => $formComponent->getSetting('name'),
            'options' => [
                'label' => $formComponent->getSetting('label'),
                'info' => $formComponent->getSetting('info'),
                'value_options' => $valueOptions,
                'empty_option' => '',
            ],
            'attributes' => [
                'required' => $formComponent->getSetting('required') ? true : false,
            ],
        ]);
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
