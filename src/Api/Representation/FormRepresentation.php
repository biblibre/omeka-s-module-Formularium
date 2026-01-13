<?php
namespace Formularium\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class FormRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLd()
    {
        return [
            'o:name' => $this->name(),
            'o:components' => $this->components(),
        ];
    }

    public function getJsonLdType()
    {
        return 'o:FormulariumSource';
    }

    public function adminUrl($action = null, $canonical = null)
    {
        $url = $this->getViewHelper('Url');
        return $url(
            'admin/formularium/form-id',
            [
                'action' => $action,
                'id' => $this->id(),
            ],
            ['force_canonical' => $canonical]
        );
    }

    public function name(): string
    {
        return $this->resource->getName();
    }

    public function components(): array
    {
        return $this->resource->getComponents();
    }

    public function actions(): array
    {
        return $this->resource->getActions();
    }

    public function submissionCount(): int
    {
        $api = $this->getServiceLocator()->get('Omeka\ApiManager');
        $response = $api->search('formularium_form_submissions', [
            'form_id' => $this->id(),
            'limit' => 0,
        ]);

        return $response->getTotalResults();
    }
}
