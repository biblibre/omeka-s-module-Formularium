<?php

namespace Formularium\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 */
class FormulariumFormActionResult extends AbstractEntity {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column(nullable=true)
     */
    protected ?string $actionInternalLabel;

    /**
     * @ManyToOne(targetEntity="Formularium\Entity\FormulariumFormSubmission")
     * @JoinColumn(onDelete="CASCADE")
     */
    protected FormulariumFormSubmission $formSubmission;

    /**
     * @Column
     */
    protected string $status;

    /**
     * @Column(type="json")
     */
    protected array $data;


    public function getId()
    {
        return $this->id;
    }

    public function getActionInternalLabel(): ?string
    {
        return $this->actionInternalLabel;
    }

    public function setActionInternalLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getFormSubmission(): FormulariumFormSubmission
    {
        return $this->formSubmission;
    }

    public function setFormSubmission(FormulariumFormSubmission $formSubmission): void
    {
        $this->formSubmission = $formSubmission;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

}
