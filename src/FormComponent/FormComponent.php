<?php
namespace Formularium\FormComponent;

use JsonSerializable;

class FormComponent implements JsonSerializable
{
    protected array $formComponent;

    public function __construct(array $formComponent)
    {
        $this->formComponent = $formComponent;
    }

    public function getType(): string
    {
        return $this->formComponent['type'];
    }

    public function getSettings(): array
    {
        return $this->formComponent['settings'] ?? [];
    }

    public function setSettings(array $settings): void
    {
        $this->formComponent['settings'] = $settings;
    }

    public function getSetting(string $name, $default = null)
    {
        $settings = $this->getSettings();
        if (array_key_exists($name, $settings)) {
            return $settings[$name];
        }

        return $default;
    }

    public function setSetting(string $name, $value): void
    {
        $this->formComponent['settings'][$name] = $value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->formComponent;
    }
}
