<?php

namespace Formularium\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class FormActionResultRepresentation extends AbstractEntityRepresentation
{

    public const STATUSES = [ self::CREATED, self::FAILED, self::SUCCEEDED, self::ERROR ];

    public const CREATED = 'created';
    public const FAILED = 'failed';
    public const SUCCEEDED = 'succeeded';
    public const ERROR = 'error';

    public function getJsonLd()
    {
        return [
            'o:action_label' => $this->getActionLabel(),
            'o:status' => $this->resource->getStatus(),
            'o:data' => $this->resource->getData()
        ];
    }

    public function getJsonLdType()
    {
        return 'o:FormlariumActionResult';
    }

    public function getActionLabel(): string {
        return $this->resource->getActionLabel();
    }

    public function getStatus(): string {
        return $this->resource->getStatus();
    }

    public function getData(): array {
        return $this->resource->getData();
    }

    public function formSubmission(): FormSubmissionRepresentation
    {
        return $this->getAdapter('formularium_form_submissions')
            ->getRepresentation($this->resource->getFormSubmission());
    }
}
