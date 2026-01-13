<?php
namespace Formularium\Api\Representation;

use DateTime;
use Omeka\Api\Representation\AbstractEntityRepresentation;

class FormSubmissionFileRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLd()
    {
        return [
            'o:storage_id' => $this->storageId(),
            'o:extension' => $this->extension(),
            'o:media_type' => $this->mediaType(),
            'o:name' => $this->name(),
        ];
    }

    public function getJsonLdType()
    {
        return 'o:FormulariumFormSubmissionFile';
    }

    public function adminUrl($action = null, $canonical = null)
    {
        $url = $this->getViewHelper('Url');

        return $url(
            'admin/formularium/form-submission-file-id',
            [
                'action' => $action,
                'id' => $this->id(),
            ],
            ['force_canonical' => $canonical]
        );
    }

    public function storageId(): string
    {
        return $this->resource->getStorageId();
    }

    public function extension(): ?string
    {
        return $this->resource->getExtension();
    }

    public function mediaType(): string
    {
        return $this->resource->getMediaType();
    }

    public function name(): string
    {
        return $this->resource->getName();
    }

    public function originalUrl(): string
    {
        return $this->getFileUrl('formularium', $this->storageId(), $this->extension());
    }
}
