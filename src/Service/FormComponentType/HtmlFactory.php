<?php
namespace Formularium\Service\FormComponentType;

use Formularium\FormComponentType\Html;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class HtmlFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');

        $html = new Html($htmlPurifier);

        return $html;
    }
}
