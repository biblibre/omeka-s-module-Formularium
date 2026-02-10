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

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        parent::settingsFieldsetAddElements($fieldset);

        $fieldset->add([
            'name' => 'filetype_allowlist',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Accepted file types', // @translate
                'info' => 'A comma-separated list of file types.<br>For instance: <code>image/jpeg,application/pdf</code>.', // @translate
                'escape_info' => false,
            ],
        ]);

        $fieldset->add([
            'name' => 'extension_allowlist',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Accepted file extensions', // @translate
                'info' => 'A comma-separated list of file extension.<br>For instance: <code>jpg,pdf</code>.', // @translate
                'escape_info' => false,
            ],
        ]);

        $fieldset->add([
            'name' => 'max_filesize',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Maximum file size', // @translate
                'info' => 'Size in bytes, or in SI notation.<br>For instance: <code>10485760</code> or <code>10MB</code>.', // @translate
                'escape_info' => false,
            ],
        ]);
    }

    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string
    {
        $element = $form->get($formComponent->getSetting('name'));

        return $renderer->formRow($element);
    }

    public function formAddElements(Form $form, FormComponent $formComponent): void
    {
        $filetype_allowlist = $formComponent->getSetting('filetype_allowlist', '');
        $extension_allowlist = $formComponent->getSetting('extension_allowlist', '');

        $filetypes = array_filter(array_map('trim', explode(',', $filetype_allowlist)));
        $extensions = array_filter(array_map('trim', explode(',', $extension_allowlist)));

        $accept_filetypes = array_map(fn($filetype) => str_contains($filetype, '/') ? $filetype : "$filetype/*", $filetypes);
        $accept_extensions = array_map(fn($extension) => ".$extension", $extensions);
        $accept = implode(',', array_merge($accept_filetypes, $accept_extensions));

        $form->add([
            'type' => 'Laminas\Form\Element\File',
            'name' => $formComponent->getSetting('name'),
            'options' => [
                'label' => $formComponent->getSetting('label'),
                'info' => $formComponent->getSetting('info'),
            ],
            'attributes' => [
                'required' => $formComponent->getSetting('required') ? true : false,
                'accept' => $accept,
            ],
        ]);
    }

    public function formAddInputFilters(InputFilterInterface $inputFilter, FormComponent $formComponent): void
    {
        $required = $formComponent->getSetting('required') ? true : false;
        $filetype_allowlist = $formComponent->getSetting('filetype_allowlist', '');
        $extension_allowlist = $formComponent->getSetting('extension_allowlist', '');
        $max_filesize = $formComponent->getSetting('max_filesize', '');

        $filetypes = array_filter(array_map('trim', explode(',', $filetype_allowlist)));
        $extensions = array_filter(array_map('trim', explode(',', $extension_allowlist)));

        $validators = [];

        if ($filetypes) {
            $validators[] = [
                'name' => 'Laminas\Validator\File\MimeType',
                'options' => [
                    'mimeType' => $filetypes,
                ],
            ];
        }

        if ($extensions) {
            $validators[] = [
                'name' => 'Laminas\Validator\File\Extension',
                'options' => [
                    'extension' => $extensions,
                ],
            ];
        }

        if ($max_filesize) {
            $validators[] = [
                'name' => 'Laminas\Validator\File\Size',
                'options' => [
                    'max' => $max_filesize,
                ],
            ];
        }

        $inputFilter->add([
            'name' => $formComponent->getSetting('name'),
            'required' => $required,
            'allow_empty' => !$required,
            'validators' => $validators,
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
