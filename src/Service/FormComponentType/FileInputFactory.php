<?php
namespace Formularium\Service\FormComponentType;

use Formularium\FormComponentType\FileInput;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FileInputFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $uploader = $serviceLocator->get('Omeka\File\Uploader');

        $fileInput = new FileInput($uploader);

        return $fileInput;
    }
}
