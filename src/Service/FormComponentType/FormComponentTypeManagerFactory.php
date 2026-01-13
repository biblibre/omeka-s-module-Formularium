<?php
namespace Formularium\Service\FormComponentType;

use Formularium\FormComponentType\FormComponentTypeManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormComponentTypeManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config = $serviceLocator->get('Config');

        return new FormComponentTypeManager($serviceLocator, $config['formularium_form_component_types'] ?? []);
    }
}
