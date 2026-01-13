<?php
namespace Formularium\FormActionType;

use Formularium\Api\Representation\FormSubmissionRepresentation;
use Formularium\FormAction\FormAction;
use Laminas\Form\Fieldset;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Stdlib\ErrorStore;

interface FormActionTypeInterface
{
    public function getLabel(): string;

    public function prepareForm(PhpRenderer $view): void;
    public function settingsFieldsetAddElements(Fieldset $fieldset): void;
    public function getSettingsFieldsetPartial(): string;
    public function hydrateFormAction(FormAction $formAction, ErrorStore $errorStore);

    public function perform(array $action, FormSubmissionRepresentation $formSubmission, array $data): void;
}
