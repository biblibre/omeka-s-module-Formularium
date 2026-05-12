<?php
namespace Formularium\Api\Representation;

use DateTime;
use Omeka\Api\Representation\AbstractEntityRepresentation;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Api\Representation\UserRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SiteRepresentation;

class FormSubmissionRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLd()
    {
        return [
            'o:data' => $this->data(),
        ];
    }

    public function getJsonLdType()
    {
        return 'o:FormulariumFormSubmission';
    }

    public function adminUrl($action = null, $canonical = null)
    {
        $url = $this->getViewHelper('Url');

        return $url(
            'admin/formularium/form-submission-id',
            [
                'action' => $action,
                'id' => $this->id(),
            ],
            ['force_canonical' => $canonical]
        );
    }

    public function form(): FormRepresentation
    {
        return $this->getAdapter('formularium_forms')->getRepresentation($this->resource->getForm());
    }

    public function site(): ?SiteRepresentation
    {
        return $this->getAdapter('sites')->getRepresentation($this->resource->getSite());
    }

    public function sitePage(): ?SitePageRepresentation
    {
        return $this->getAdapter('site_pages')->getRepresentation($this->resource->getSitePage());
    }

    public function resource(): ?AbstractResourceEntityRepresentation
    {
        return $this->getAdapter('resources')->getRepresentation($this->resource->getResource());
    }

    public function submitted(): DateTime
    {
        return $this->resource->getSubmitted();
    }

    public function submitter(): ?UserRepresentation
    {
        $submitter = $this->resource->getSubmitter();

        return $submitter ? $this->getAdapter('users')->getRepresentation($submitter) : null;
    }

    public function handled(): ?DateTime
    {
        return $this->resource->getHandled();
    }

    public function handler(): ?UserRepresentation
    {
        $handler = $this->resource->getHandler();

        return $handler ? $this->getAdapter('users')->getRepresentation($handler) : null;
    }

    public function submitterEmail(): ?string
    {
        return $this->resource->getSubmitterEmail();
    }

    public function data(): array
    {
        return $this->resource->getData();
    }

    public function files(): array
    {
        $adapter = $this->getAdapter('formularium_form_submission_files');
        $files = [];
        foreach ($this->resource->getFiles() as $file) {
            $files[] = $adapter->getRepresentation($file);
        }

        return $files;
    }

    public function actionResults(): array
    {
        $services = $this->getServiceLocator();
        $logger = $services->get("Omeka\Logger");
        $adapter = $this->getAdapter('formularium_form_action_result');

        $actionResults = [];
        foreach ( $this->resource->getActionResults() as $actionResult)
        {
            $actionResults[] = $adapter->getRepresentation($actionResult);
        }
        return $actionResults;
    }

    public function successfullActionCount(): int 
    {
        $api = $this->getServiceLocator()->get('Omeka\ApiManager');
        $response = $api->search('formularium_form_action_result', [
            'form_submission_id' => $this->id(),
            'status' => [FormActionResultRepresentation::SUCCEEDED],
            'limit' => 0,
        ]);
        return $response->getTotalResults();
    }

    public function actionCount(): int 
    {
        return count($this->resource->getActionResults());
    }

}

