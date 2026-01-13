<?php
namespace Formularium\Service\Controller\Plugin;

use Formularium\Controller\Plugin\Formularium;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormulariumFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $formComponentTypeManager = $serviceLocator->get('Formularium\FormComponentTypeManager');
        $formActionTypeManager = $serviceLocator->get('Formularium\FormActionTypeManager');

        return new Formularium($formComponentTypeManager, $formActionTypeManager);
    }
}
