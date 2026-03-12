<?php
namespace Formularium\Service\Form\Element;

use Formularium\Form\Element\SubmitterSelect;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class SubmitterSelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $element = new SubmitterSelect(null, $options ?? []);

        $element->setConnection($serviceLocator->get('Omeka\Connection'));
        $element->setApiManager($serviceLocator->get('Omeka\ApiManager'));

        return $element;
    }
}
