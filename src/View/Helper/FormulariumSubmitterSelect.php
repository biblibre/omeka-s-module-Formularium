<?php
namespace Formularium\View\Helper;

use Formularium\Form\Element\SubmitterSelect;
use Laminas\Form\Factory;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Form\FormElementManager;

class FormulariumSubmitterSelect extends AbstractHelper
{
    public function __construct(protected FormElementManager $formElementManager)
    {
    }

    public function __invoke(array $spec = [])
    {
        $spec['type'] = SubmitterSelect::class;
        if (!isset($spec['options']['empty_option'])) {
            $spec['options']['empty_option'] = 'Select user…'; // @translate
        }
        $factory = new Factory($this->formElementManager);
        $element = $factory->createElement($spec);

        return $this->getView()->formSelect($element);
    }
}
