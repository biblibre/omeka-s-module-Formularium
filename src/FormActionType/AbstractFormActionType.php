<?php
namespace Formularium\FormActionType;

use Formularium\FormAction\FormAction;
use Laminas\Form\Fieldset;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\ServiceManager\SortableInterface;
use Omeka\Stdlib\ErrorStore;

abstract class AbstractFormActionType implements FormActionTypeInterface, SortableInterface
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
        return 'formularium/common/form-action-settings/default';
    }

    public function hydrateFormAction(FormAction $formAction, ErrorStore $errorStore)
    {
    }
}
