<?php

namespace Formularium\Entity;

use DateTime;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\User;

/**
 * @Entity
 */
class FormulariumFormSubmissionFile extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Formularium\Entity\FormulariumFormSubmission")
     * @JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected FormulariumFormSubmission $formSubmission;

    /**
     * @Column
     */
    protected $name;

    /**
     * @Column
     */
    protected $mediaType;

    /**
     * @Column(length=190, unique=true)
     */
    protected $storageId;

    /**
     * @Column(nullable=true)
     */
    protected $extension;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFormSubmission(): FormulariumFormSubmission
    {
        return $this->formSubmission;
    }

    public function setFormSubmission(FormulariumFormSubmission $formSubmission): void
    {
        $this->formSubmission = $formSubmission;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setMediaType(string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function getFilename(): string
    {
        $filename = $this->storageId;
        if ($filename !== null && $this->extension !== null) {
            $filename .= '.' . $this->extension;
        }

        return $filename;
    }

    public function setStorageId(string $storageId): void
    {
        $this->storageId = $storageId;
    }

    public function getStorageId(): string
    {
        return $this->storageId;
    }

    public function setExtension(?string $extension): void
    {
        $this->extension = $extension;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }
}
