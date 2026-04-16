<?php

namespace Formularium\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 */
class FormulariumForm extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column
     */
    protected string $name;

    /**
     * @Column
     */
    protected string $resourcePageBlockTitle;

    /**
     * @Column(type="json")
     */
    protected array $components;

    /**
     * @Column(type="json")
     */
    protected array $actions;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getResourcePageBlockTitle(): string
    {
        return $this->resourcePageBlockTitle;
    }

    public function setResourcePageBlockTitle(string $resourcePageBlockTitle): void
    {
        $this->resourcePageBlockTitle = $resourcePageBlockTitle;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function setComponents(array $components): void
    {
        $this->components = $components;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }
}
