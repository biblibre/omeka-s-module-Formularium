<?php
namespace Formularium\FormActionType;

use Omeka\ServiceManager\AbstractPluginManager;

class FormActionTypeManager extends AbstractPluginManager
{
    protected $instanceOf = FormActionTypeInterface::class;
}
