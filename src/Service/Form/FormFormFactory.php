<?php
namespace Formularium\Service\Form;

use Formularium\Form\FormForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $form = new FormForm(null, $options ?? []);

        // TODO Remove this useless factory
        return $form;
    }
}
