<?php
namespace Formularium\Service\View\Helper;

use Formularium\View\Helper\Formularium;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormulariumFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $formElementManager = $serviceLocator->get('FormElementManager');
        $formComponentTypeManager = $serviceLocator->get('Formularium\FormComponentTypeManager');
        $formActionTypeManager = $serviceLocator->get('Formularium\FormActionTypeManager');

        return new Formularium($formElementManager, $formComponentTypeManager, $formActionTypeManager);
    }
}
