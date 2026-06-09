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
     * @Column(type="json")
     */
    protected array $components;

    /**
     * @Column(type="json")
     */
    protected array $actions;

    /**
     * @Column(type="json")
     */
    protected array $settings;

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

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function getSetting(string $name, mixed $default = null): mixed
    {
        if (!array_key_exists($name, $this->settings)) {
            return $default;
        }

        return $this->settings[$name];
    }
}
