<?php
namespace Formularium\Service\FormActionType;

use Formularium\FormActionType\FormActionTypeManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormActionTypeManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config = $serviceLocator->get('Config');

        return new FormActionTypeManager($serviceLocator, $config['formularium_form_action_types'] ?? []);
    }
}
