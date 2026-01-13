<?php
namespace Formularium\Form\View\Helper;

use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\Form\ElementInterface;

class FormComponents extends AbstractHelper
{
    public function __invoke(ElementInterface $element)
    {
        return $this->render($element);
    }

    public function render(ElementInterface $element)
    {
        $view = $this->getView();

        return $view->partial('formularium/common/components-form', ['element' => $element]);
    }
}
