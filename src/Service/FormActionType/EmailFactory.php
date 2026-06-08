<?php
namespace Formularium\Service\FormActionType;

use Formularium\FormActionType\Email;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class EmailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        return new Email(
            $serviceLocator->get('Omeka\Mailer'),
            $serviceLocator->get('MvcTranslator'),
            $serviceLocator->get('Omeka\Logger'),
        );
    }
}
