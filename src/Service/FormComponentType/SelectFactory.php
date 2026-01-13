<?php
namespace Formularium\Service\FormComponentType;

use Formularium\FormComponentType\Select;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class SelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $api = $serviceLocator->get('Omeka\ApiManager');

        $select = new Select($api);

        return $select;
    }
}
