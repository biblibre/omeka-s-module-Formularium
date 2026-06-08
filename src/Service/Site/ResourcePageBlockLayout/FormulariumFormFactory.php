<?php

namespace Formularium\Service\Site\ResourcePageBlockLayout;

use Formularium\Site\ResourcePageBlockLayout\FormulariumForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Omeka\Service\Exception\ConfigException;

class FormulariumFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        if (!preg_match('/^formulariumForm:(\d+)$/', $requestedName, $matches)) {
            throw new ConfigException('Invalid service name: ' . $requestedName);
        }

        $id = $matches[1];
        $api = $serviceLocator->get('Omeka\ApiManager');
        $formulariumForm = $api->read('formularium_forms', $id)->getContent();
        $service = new FormulariumForm($formulariumForm);

        return $service;
    }
}
