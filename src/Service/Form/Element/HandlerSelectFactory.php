<?php
namespace Formularium\Service\Form\Element;

use Formularium\Form\Element\HandlerSelect;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class HandlerSelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $element = new HandlerSelect(null, $options ?? []);

        $element->setConnection($serviceLocator->get('Omeka\Connection'));
        $element->setApiManager($serviceLocator->get('Omeka\ApiManager'));

        return $element;
    }
}
