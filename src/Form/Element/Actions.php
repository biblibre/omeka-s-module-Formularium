<?php
namespace Formularium\Form\Element;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;

class Actions extends Element implements InputProviderInterface
{
    public function getInputSpecification()
    {
        return [
            'required' => false,
        ];
    }
}
