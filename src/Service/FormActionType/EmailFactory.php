<?php
namespace Formularium\Service\FormActionType;

use Formularium\FormActionType\Email;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class EmailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $mailer = $serviceLocator->get('Omeka\Mailer');

        $email = new Email($mailer);

        return $email;
    }
}
