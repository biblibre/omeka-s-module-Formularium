<?php
namespace Formularium\Service\Form;

use Formularium\Form\FormulariumForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FormulariumFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $form = new FormulariumForm(null, $options ?? []);

        $form->setFormComponentTypeManager($serviceLocator->get('Formularium\FormComponentTypeManager'));

        return $form;
    }
}
