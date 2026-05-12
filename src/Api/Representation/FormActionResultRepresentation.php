<?php

namespace Formularium\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class FormActionResultRepresentation extends AbstractEntityRepresentation
{

    public const STATUSES = [ self::CREATED, self::FAILED, self::SUCCEEDED ];

    public const CREATED = 'created';
    public const FAILED = 'failed';
    public const SUCCEEDED = 'succeeded';

    public function getJsonLd()
    {
        return [
            'o:action_internal_label' => $this->resource->getActionInternalLabel(),
            'o:status' => $this->resource->getStatus(),
            'o:data' => $this->resource->getData()
        ];
    }

    public function getJsonLdType()
    {
        return 'o:FormlariumActionResult';
    }

    public function adminUrl($action = null, $canonical = null)
    {
        $url = $this->getViewHelper('Url');
        return $url('admin/formularium/form-action-result-id',
            [
                'action' => $action,
                'id' => $this->id(),
            ],
            ['force_canonical' => $canonical]
        );
    }

    public function formSubmission(): FormSubmissionRepresentation
    {
        return $this->getAdapter('formularium_form_submissions')
            ->getRepresentation($this->resource->getFormSubmission());
    }
}
