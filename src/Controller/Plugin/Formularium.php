<?php
namespace Formularium\Controller\Plugin;

use Formularium\Api\Representation\FormRepresentation;
use Formularium\FormComponentType\FormComponentTypeManager;
use Formularium\FormComponentType\FormComponentTypeInterface;
use Formularium\FormActionType\FormActionTypeManager;
use Formularium\FormActionType\FormActionTypeInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class Formularium extends AbstractPlugin
{
    protected FormComponentTypeManager $formComponentTypeManager;
    protected FormActionTypeManager $formActionTypeManager;

    public function __construct(FormComponentTypeManager $formComponentTypeManager, FormActionTypeManager $formActionTypeManager)
    {
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
}
