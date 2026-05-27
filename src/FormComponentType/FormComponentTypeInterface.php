<?php
namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;

interface FormComponentTypeInterface
{
    /**
     * The label of this component
     */
    public function getLabel(): string;

    /**
     * Allows adding some front-end dependencies 
     * needed by the action in the admin configuration form.
     */
    public function prepareForm(PhpRenderer $view): void;

    /**
     * Configure the fieldset for the configuration form on the admin interface.
     */
    public function settingsFieldsetAddElements(Fieldset $fieldset): void;

    /**
     * Returns the template path of the partial document of this form component. 
     */
    public function getSettingsFieldsetPartial(): string;

    /**
     * Hydrate the FormulariumFormComponent part on the FormulariumForm entity.
     */
    public function hydrateFormComponent(FormComponent $formComponent, ErrorStore $errorStore);

    /**
     * Function rendering the component. Called inside the formularium-form partial template.
     */
    public function render(PhpRenderer $renderer, Form $form, FormComponent $formComponent, $data = null): string;

    /**
     *  Add the elements to the Laminas Form in the block for the site.
     */
    public function formAddElements(Form $form, FormComponent $formComponent): void;

    /**
     * Add the input filters to the form component.
     */
    public function formAddInputFilters(InputFilterInterface $inputFilter, FormComponent $formComponent): void;

    /**
     * Fill out the form FormulariumFormSubmission from the data
     * in the form from the api.
     */
    public function hydrateFormSubmission(
        FormComponent $formComponent,
        Request $request,
        FormulariumFormSubmission $formSubmission,
        ErrorStore $errorStore
    );
}
