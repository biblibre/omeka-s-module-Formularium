<?php

namespace Formularium\Site\ResourcePageBlockLayout;

use Formularium\Api\Representation\FormRepresentation;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Site\ResourcePageBlockLayout\ResourcePageBlockLayoutInterface;
use Laminas\View\Renderer\PhpRenderer;

class FormulariumForm implements ResourcePageBlockLayoutInterface
{
    public function __construct(
        protected FormRepresentation $formulariumForm
    ) {}

    public function getLabel(): string
    {
        return sprintf('Formularium: %s', $this->formulariumForm->name());
    }

    public function getCompatibleResourceNames(): array
    {
        return ['items', 'media', 'item_sets'];
    }

    public function render(PhpRenderer $view, AbstractResourceEntityRepresentation $resource): string
    {
        $form = $view->formularium()->getForm($this->formulariumForm);
        $form->get('formularium_resource_id')->setAttribute('value', $resource->id());

        $values = [
            'formulariumForm' => $this->formulariumForm,
            'resource' => $resource,
            'form' => $form,
        ];

        return $view->partial('formularium/common/resource-page-block-layout/formularium-form', $values);
    }
}
