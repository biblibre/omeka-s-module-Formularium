<?php
namespace Formularium\View\Helper;

use Formularium\Api\Representation\FormRepresentation;
use Formularium\FormComponentType\FormComponentTypeManager;
use Formularium\FormComponentType\FormComponentTypeInterface;
use Formularium\FormActionType\FormActionTypeManager;
use Formularium\FormActionType\FormActionTypeInterface;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Form\FormElementManager;
use Laminas\Form\Form;

class Formularium extends AbstractHelper
{
    protected FormElementManager $formElementManager;
    protected FormComponentTypeManager $formComponentTypeManager;
    protected FormActionTypeManager $formActionTypeManager;

    public function __construct(FormElementManager $formElementManager, FormComponentTypeManager $formComponentTypeManager, FormActionTypeManager $formActionTypeManager)
    {
        $this->formElementManager = $formElementManager;
        $this->formComponentTypeManager = $formComponentTypeManager;
        $this->formActionTypeManager = $formActionTypeManager;
    }

    public function getFormComponentTypes(): array
    {
        $formComponentTypes = [];
        $names = $this->formComponentTypeManager->getRegisteredNames(true);
        foreach ($names as $name) {
            $formComponentType = $this->formComponentTypeManager->get($name);
            $formComponentTypes[$name] = $formComponentType;
        }

        return $formComponentTypes;
    }

    public function getFormComponentType(string $type): ?FormComponentTypeInterface
    {
        if (!$this->formComponentTypeManager->has($type)) {
            return null;
        }

        return $this->formComponentTypeManager->get($type);
    }

    public function formComponentSettingsForm(array $formComponent): string
    {
        $view = $this->getView();

        $formComponentType = $this->getFormComponentType($formComponent['type']);
        if (!$formComponentType) {
            return '';
        }

        $partial = $formComponentType->getSettingsFieldsetPartial();
        $values = [
            'formComponent' => $formComponent,
            'formComponentType' => $formComponentType,
        ];

        return $view->partial($partial, $values);
    }

    public function getFormActionTypes(): array
    {
        $formActionTypes = [];
        $names = $this->formActionTypeManager->getRegisteredNames(true);
        foreach ($names as $name) {
            $formActionType = $this->formActionTypeManager->get($name);
            $formActionTypes[$name] = $formActionType;
        }

        return $formActionTypes;
    }

    public function getFormActionType(string $type): ?FormActionTypeInterface
    {
        if (!$this->formActionTypeManager->has($type)) {
            return null;
        }

        return $this->formActionTypeManager->get($type);
    }

    public function formActionSettingsForm(array $formAction): string
    {
        $view = $this->getView();

        $formActionType = $this->getFormActionType($formAction['type']);
        if (!$formActionType) {
            return '';
        }

        $partial = $formActionType->getSettingsFieldsetPartial();
        $values = [
            'formAction' => $formAction,
            'formActionType' => $formActionType,
        ];

        return $view->partial($partial, $values);
    }

    public function renderForm(FormRepresentation $formulariumForm): string
    {
        $view = $this->getView();
        $form = $this->getForm($formulariumForm);

        return $view->partial('formularium/common/formularium-form', ['form' => $form, 'formulariumForm' => $formulariumForm]);
    }

    public function getForm(FormRepresentation $formulariumForm): Form
    {
        $view = $this->getView();
        $site = $view->currentSite();

        $form = $this->formElementManager->get('Formularium\Form\FormulariumForm', ['formularium_form' => $formulariumForm]);
        $form->setAttribute('action', $view->url('site/formularium/form-id', ['id' => $formulariumForm->id(), 'site-slug' => $site->slug()]));

        return $form;
    }
}
