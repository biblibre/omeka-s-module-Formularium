<?php
namespace Formularium\FormComponentType;

use Formularium\Entity\FormulariumFormSubmission;
use Formularium\FormComponent\FormComponent;
use Laminas\Form\Fieldset;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\ServiceManager\SortableInterface;
use Omeka\Api\Request;
use Omeka\Stdlib\ErrorStore;

abstract class AbstractFormComponentType implements FormComponentTypeInterface, SortableInterface
{
    public function getSortableString()
    {
        return $this->getLabel();
    }

    public function prepareForm(PhpRenderer $view): void
    {
    }

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
    }

    public function getSettingsFieldsetPartial(): string
    {
        return 'formularium/common/form-component-settings/default';
    }

    public function hydrateFormComponent(FormComponent $formComponent, ErrorStore $errorStore)
    {
    }

    public function formAddElements(Form $form, FormComponent $formComponent): void
    {
    }

    public function formAddInputFilters(InputFilterInterface $inputFilter, FormComponent $formComponent): void
    {
    }

    public function hydrateFormSubmission(FormComponent $formComponent, Request $request, FormulariumFormSubmission $formSubmission, ErrorStore $errorStore)
    {
    }
}
