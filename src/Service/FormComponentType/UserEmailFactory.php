<?php
namespace Formularium\Service\FormComponentType;

use Formularium\FormComponentType\UserEmail;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class UserEmailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $authenticationService = $serviceLocator->get('Omeka\AuthenticationService');

        $userEmail = new UserEmail($authenticationService);

        return $userEmail;
    }
}
