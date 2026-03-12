<?php
namespace Formularium\Service\View\Helper;

use Formularium\View\Helper\FormulariumHandlerSelect;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormulariumHandlerSelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $formElementManager = $serviceLocator->get('FormElementManager');

        return new FormulariumHandlerSelect($formElementManager);
    }
}
