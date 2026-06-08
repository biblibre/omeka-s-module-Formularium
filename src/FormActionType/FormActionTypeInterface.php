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
     * The label of this action type.
     */
    public function getLabel(): string;

    /**
     * Allows adding some front-end dependencies to the page
     * needed by the action in the admin configuration form.
     */
    public function prepareForm(PhpRenderer $view): void;

    /**
     * Configure the fieldset for the configuration form on the admin interface.
     */
    public function settingsFieldsetAddElements(Fieldset $fieldset): void;

    /**
     * Returns the template for the partial element for this action.
     */
    public function getSettingsFieldsetPartial(): string;

    /**
     * If the action needs custom hydration logic. 
     * For persistance in database.
     */
    public function hydrateFormAction(
        FormAction $formAction,
        ErrorStore $errorStore,
    );

    /**
     * Here the action is performed. 
     * The status field in actionResult must be updated.
     * The settings of the action are available in the 'settings' key
     * of the action argument. 
     */
    public function perform(
        array $action,
        FormSubmissionRepresentation $formSubmission,
        array $data,
    ): array;
}
