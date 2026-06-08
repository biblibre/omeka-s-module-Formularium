<?php

namespace Formularium\Service\FormActionType;

use Formularium\FormActionType\CreateUser;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class CreateUserFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $serviceLocator,
        $requestedName,
        ?array $options = null,
    ) {
        return new CreateUser(
            $serviceLocator->get('Omeka\Mailer'),
            $serviceLocator->get('Omeka\ApiManager'),
            $serviceLocator->get('Omeka\Acl'),
            $serviceLocator->get('Omeka\ModuleManager'),
            $serviceLocator->get('Omeka\Logger'),
        );
    }
}
