<?php
namespace Formularium\FormComponentType;

use Omeka\ServiceManager\AbstractPluginManager;

class FormComponentTypeManager extends AbstractPluginManager
{
    protected $instanceOf = FormComponentTypeInterface::class;
}
