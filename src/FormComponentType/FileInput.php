<?php

namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\Entity\FormulariumFormSubmissionFile;
use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Request;
use Omeka\File\Uploader;
use Omeka\Stdlib\ErrorStore;

class FileInput extends AbstractInput
{
    protected Uploader $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function getLabel(): string
    {
        return 'File upload'; // @translate
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        $element = $form->get($formComponent->getSetting('name'));

        return $renderer->formRow($element);
    }

    public function formAddElements(Form $form, FormComponent $formComponent): void
    {
        $form->add([
            'type' => 'Laminas\Form\Element\File',
            'name' => $formComponent->getSetting('name'),
            'options' => [
                'label' => $formComponent->getSetting('label'),
                'info' => $formComponent->getSetting('info'),
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

        $fileData = $request->getFileData();
        if (!$fileData[$name]) {
            return;
        }

        if (!array_is_list($fileData[$name])) {
            $fileData[$name] = [$fileData[$name]];
        }

        foreach ($fileData[$name] as $i => $file) {
            if ($file['error'] === 4) {
                continue;
            }

            $tempFile = $this->uploader->upload($file, $errorStore);
            if (!$tempFile) {
                return;
            }

            $tempFile->setSourceName($file['name']);

            // TODO Validate file type
            //$config = $this->getServiceLocator()->get('Config');
            //$validator = new Validator($config['api_assets']['allowed_media_types'], $config['api_assets']['allowed_extensions']);
            //if (!$validator->validate($tempFile, $errorStore)) {
            //    return;
            //}

            $formSubmissionFile = new FormulariumFormSubmissionFile;
            $formSubmissionFile->setFormSubmission($formSubmission);
            $formSubmissionFile->setStorageId($tempFile->getStorageId());
            $formSubmissionFile->setExtension($tempFile->getExtension());
            $formSubmissionFile->setMediaType($tempFile->getMediaType());
            $formSubmissionFile->setName($tempFile->getSourceName());

            $tempFile->store('formularium');
            $tempFile->delete();

            $formSubmission->getFiles()->add($formSubmissionFile);
        }
    }
}
