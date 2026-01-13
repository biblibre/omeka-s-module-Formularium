<?php
namespace Formularium\FormAction;

use JsonSerializable;

class FormAction implements JsonSerializable
{
    public function __construct(protected array $action)
    {
    }

    public function getType(): string
    {
        return $this->action['type'];
    }

    public function getSettings(): array
    {
        return $this->action['settings'] ?? [];
    }

    public function setSettings(array $settings): void
    {
        $this->action['settings'] = $settings;
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
        $this->action['settings'][$name] = $value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->action;
    }
}
