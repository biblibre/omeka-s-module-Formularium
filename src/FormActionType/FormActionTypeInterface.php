<?php
namespace Formularium\FormActionType;

use Formularium\Api\Representation\FormSubmissionRepresentation;
use Formularium\Api\Representation\FormActionResultRepresentation;
use Formularium\FormAction\FormAction;
use Laminas\Form\Fieldset;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Stdlib\ErrorStore;

interface FormActionTypeInterface
{
    /**
     * The label of this action type interface.
     */
    public function getLabel(): string;

    /**
     * 
     */
    public function prepareForm(PhpRenderer $view): void;

    /**
     * Here the field set of the action setting configuration
     * form must be filled.
     */
    public function settingsFieldsetAddElements(Fieldset $fieldset): void;

    /**
     * 
     */
    public function getSettingsFieldsetPartial(): string;

    /**
     * 
     */
    public function hydrateFormAction(
        FormAction $formAction,
        ErrorStore $errorStore,
    );

    /**
     * Here the action is performed. 
     * And the status field in actionResult must be updated.
     * The settings of the action are available in the 'settings' key
     * of the action argument. 
     */
    public function perform(
        array $action,
        FormSubmissionRepresentation $formSubmission,
        array $data,
    ): array;
}
