<?php

namespace Formularium\Site\BlockLayout;

use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Laminas\View\Renderer\PhpRenderer;

class Formularium extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'Formularium';
    }

    public function form(PhpRenderer $view, SiteRepresentation $site, SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null)
    {
        $forms = $view->api()->search('formularium_forms')->getContent();
        $valueOptions = [];
        foreach ($forms as $form) {
            $valueOptions[$form->id()] = $form->name();
        }

        $select = new \Laminas\Form\Element\Select('o:block[__blockIndex__][o:data][formularium_form_id]');
        $select->setLabel('Form'); // @translate
        $select->setValueOptions($valueOptions);
        $select->setEmptyOption('');
        $select->setAttribute('required', true);
        $select->setValue($block ? $block->dataValue('formularium_form_id') : '');

        return $view->formRow($select);
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $serviceLocator = $block->getServiceLocator();
        $api = $serviceLocator->get('Omeka\ApiManager');
        $request = $serviceLocator->get('Application')->getRequest();

        $formularium_form_id = $block->dataValue('formularium_form_id');
        $formulariumForm = $api->read('formularium_forms', $formularium_form_id)->getContent();
        $form = $view->formularium()->getForm($formulariumForm);
        $form->get('formularium_site_page_block_id')->setAttribute('value', $block->id());
        $form->get('formularium_site_page_id')->setAttribute('value', $block->page()->id());

        $values = [
            'block' => $block,
            'form' => $form,
            'formulariumForm' => $formulariumForm,
        ];

        return $view->partial('formularium/common/block-layout/formularium', $values);
    }
}
