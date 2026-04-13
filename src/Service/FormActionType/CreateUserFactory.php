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
        array $options = null,
    ) {
        $mailer = $serviceLocator->get('Omeka\Mailer');
        $api = $serviceLocator->get('Omeka\ApiManager');
        $acl = $serviceLocator->get('Omeka\Acl');
        $createUser = new CreateUser($mailer, $api, $acl);

        return $createUser;
    }
}
