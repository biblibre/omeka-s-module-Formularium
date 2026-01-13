<?php
namespace Formularium\Service\Delegator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Map custom element types to the view helpers that render them.
 */
class FormElementDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name,
        callable $callback, array $options = null
    ) {
        $formElement = $callback();
        $formElement->addClass('Formularium\Form\Element\Components', 'formulariumFormComponents');
        $formElement->addClass('Formularium\Form\Element\Actions', 'formulariumFormActions');
        return $formElement;
    }
}
