<?php

namespace Formularium\Controller\Admin;

use Formularium\Form\FormForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Omeka\Form\ConfirmForm;
use Omeka\Stdlib\Message;

class FormController extends AbstractActionController
{
    public function browseAction()
    {
        $this->browse()->setDefaults('formularium_forms');

        $response = $this->api()->search('formularium_forms', $this->params()->fromQuery());
        $this->paginator($response->getTotalResults());

        $forms = $response->getContent();

        $view = new ViewModel();
        $view->setVariable('forms', $forms);

        return $view;
    }

    public function addAction()
    {
        $form = $this->getForm(FormForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                unset($data['csrf']);

                $response = $this->api($form)->create('formularium_forms', $data);
                if ($response) {
                    $this->messenger()->addSuccess('Form successfully created.'); // @translate

                    return $this->redirect()->toRoute('admin/formularium/form');
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);

        return $view;
    }

    public function editAction()
    {
        $id = $this->params('id');

        $formulariumForm = $this->api()->read('formularium_forms', $id)->getContent();

        $form = $this->getForm(FormForm::class, ['form' => $formulariumForm]);
        $form->setData([
            'o:name' => $formulariumForm->name(),
            'o:components' => $formulariumForm->components(),
            'o:actions' => $formulariumForm->actions(),
        ]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                unset($data['csrf']);

                $response = $this->api($form)->update('formularium_forms', $id, $data);
                if ($response) {
                    $this->messenger()->addSuccess('Form successfully updated.'); // @translate

                    return $this->redirect()->toRoute('admin/formularium/form');
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);

        return $view;
    }

    public function deleteConfirmAction()
    {
        $resource = $this->api()->read('formularium_forms', $this->params('id'))->getContent();

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setTemplate('common/delete-confirm-details');
        $view->setVariable('resource', $resource);
        $view->setVariable('resourceLabel', 'form'); // @translate
        $view->setVariable('partialPath', 'formularium/admin/form/show-details');

        return $view;
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(ConfirmForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $response = $this->api($form)->delete('formularium_forms', $this->params('id'));
                if ($response) {
                    $this->messenger()->addSuccess('Form successfully deleted'); // @translate
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        return $this->redirect()->toRoute('admin/formularium/form');
    }

    public function showDetailsAction()
    {
        $response = $this->api()->read('formularium_forms', $this->params('id'));

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('resource', $response->getContent());

        return $view;
    }

    public function componentRowAction()
    {
    }
}
