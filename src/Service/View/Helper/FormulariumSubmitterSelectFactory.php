<?php
namespace Formularium\Service\View\Helper;

use Formularium\View\Helper\FormulariumSubmitterSelect;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormulariumSubmitterSelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $formElementManager = $serviceLocator->get('FormElementManager');

        return new FormulariumSubmitterSelect($formElementManager);
    }
}
