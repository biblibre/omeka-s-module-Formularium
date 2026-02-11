<?php

namespace Formularium\Controller\Admin;

use Formularium\Form\FormForm;
use Formularium\Form\FormSubmissionBatchUpdateForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Omeka\Form\ConfirmForm;
use Omeka\Stdlib\Message;

class FormSubmissionController extends AbstractActionController
{
    public function browseAction()
    {
        $this->browse()->setDefaults('formularium_form_submissions');

        $response = $this->api()->search('formularium_form_submissions', $this->params()->fromQuery());
        $this->paginator($response->getTotalResults());

        $formSubmissions = $response->getContent();

        $returnQuery = $this->params()->fromQuery();
        unset($returnQuery['page']);

        $formDeleteSelected = $this->getForm(ConfirmForm::class);
        $formDeleteSelected->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'batch-delete'], ['query' => $returnQuery], true));
        $formDeleteSelected->setButtonLabel('Confirm Delete'); // @translate
        $formDeleteSelected->setAttribute('id', 'confirm-delete-selected');

        $formDeleteAll = $this->getForm(ConfirmForm::class);
        $formDeleteAll->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'batch-delete-all'], ['query' => $returnQuery], true));
        $formDeleteAll->setButtonLabel('Confirm Delete'); // @translate
        $formDeleteAll->setAttribute('id', 'confirm-delete-all');
        $formDeleteAll->get('submit')->setAttribute('disabled', true);

        $view = new ViewModel();
        $view->setVariable('formSubmissions', $formSubmissions);
        $view->setVariable('formDeleteSelected', $formDeleteSelected);
        $view->setVariable('formDeleteAll', $formDeleteAll);
        $view->setVariable('returnQuery', $returnQuery);

        return $view;
    }

    public function searchAction()
    {
        $view = new ViewModel;
        $view->setVariable('query', $this->params()->fromQuery());

        return $view;
    }

    public function markAsHandledConfirmAction()
    {
        $resource = $this->api()->read('formularium_form_submissions', $this->params('id'))->getContent();

        $form = $this->getForm(ConfirmForm::class);
        $form->setAttribute('action', $resource->url('mark-as-handled'));

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('resource', $resource);
        $view->setVariable('form', $form);

        return $view;
    }

    public function markAsHandledAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(ConfirmForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->api()->update('formularium_form_submissions', $this->params('id'), ['o:handled' => true], [], ['isPartial' => true]);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        return $this->redirect()->toRoute('admin/formularium/form-submission');
    }

    public function markAsNotHandledConfirmAction()
    {
        $resource = $this->api()->read('formularium_form_submissions', $this->params('id'))->getContent();

        $form = $this->getForm(ConfirmForm::class);
        $form->setAttribute('action', $resource->url('mark-as-not-handled'));

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('resource', $resource);
        $view->setVariable('form', $form);

        return $view;
    }

    public function markAsNotHandledAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(ConfirmForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->api()->update('formularium_form_submissions', $this->params('id'), ['o:handled' => false], [], ['isPartial' => true]);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        return $this->redirect()->toRoute('admin/formularium/form-submission');
    }

    public function deleteConfirmAction()
    {
        $resource = $this->api()->read('formularium_form_submissions', $this->params('id'))->getContent();

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setTemplate('common/delete-confirm-details');
        $view->setVariable('resource', $resource);
        $view->setVariable('resourceLabel', 'form submission'); // @translate
        $view->setVariable('partialPath', 'formularium/admin/form-submission/show-details');

        return $view;
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(ConfirmForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $response = $this->api($form)->delete('formularium_form_submissions', $this->params('id'));
                if ($response) {
                    $this->messenger()->addSuccess('Form submission successfully deleted'); // @translate
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        return $this->redirect()->toRoute('admin/formularium/form-submission');
    }

    public function batchDeleteAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
        }

        $returnQuery = $this->params()->fromQuery();
        $formSubmissionIds = $this->params()->fromPost('form_submission_ids', []);
        if (!$formSubmissionIds) {
            $this->messenger()->addError('You must select at least one form submission to batch delete.'); // @translate
            return $this->redirect()->toRoute(null, ['action' => 'browse'], ['query' => $returnQuery], true);
        }

        $form = $this->getForm(ConfirmForm::class);
        $form->setData($this->getRequest()->getPost());
        if ($form->isValid()) {
            $response = $this->api($form)->batchDelete('formularium_form_submissions', $formSubmissionIds, [], ['continueOnError' => true]);
            if ($response) {
                $this->messenger()->addSuccess('Form submissions successfully deleted'); // @translate
            }
        } else {
            $this->messenger()->addFormErrors($form);
        }
        return $this->redirect()->toRoute(null, ['action' => 'browse'], ['query' => $returnQuery], true);
    }

    public function batchDeleteAllAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
        }

        // Derive the query, removing limiting and sorting params.
        $query = json_decode($this->params()->fromPost('query', []), true);
        unset($query['submit'], $query['page'], $query['per_page'], $query['limit'],
            $query['offset'], $query['sort_by'], $query['sort_order']);

        $form = $this->getForm(ConfirmForm::class);
        $form->setData($this->getRequest()->getPost());
        if ($form->isValid()) {
            $job = $this->jobDispatcher()->dispatch('Omeka\Job\BatchDelete', [
                'resource' => 'formularium_form_submissions',
                'query' => $query,
            ]);
            $this->messenger()->addSuccess('Deleting form submissions. This may take a while.'); // @translate
        } else {
            $this->messenger()->addFormErrors($form);
        }
        return $this->redirect()->toRoute(null, ['action' => 'browse'], ['query' => $this->params()->fromQuery()], true);
    }

    public function showDetailsAction()
    {
        $response = $this->api()->read('formularium_form_submissions', $this->params('id'));

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('resource', $response->getContent());

        return $view;
    }

    public function batchEditAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
        }

        $returnQuery = $this->params()->fromQuery();
        $formSubmissionIds = $this->params()->fromPost('form_submission_ids', []);
        if (!$formSubmissionIds) {
            $this->messenger()->addError('You must select at least one form submission to batch edit.'); // @translate
            return $this->redirect()->toRoute(null, ['action' => 'browse'], ['query' => $returnQuery], true);
        }

        $form = $this->getForm(FormSubmissionBatchUpdateForm::class);
        $form->setAttribute('id', 'batch-edit-form-submission');
        if ($this->params()->fromPost('batch_update')) {
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                $this->api($form)->batchUpdate('formularium_form_submissions', $formSubmissionIds, $data, [
                    'continueOnError' => true,
                    'detachEntities' => false,
                ]);

                $this->messenger()->addSuccess('Form submission successfully edited'); // @translate
                return $this->redirect()->toRoute(null, ['action' => 'browse'], ['query' => $returnQuery], true);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $formSubmissions = [];
        foreach ($formSubmissionIds as $formSubmissionId) {
            $formSubmissions[] = $this->api()->read('formularium_form_submissions', $formSubmissionId)->getContent();
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('formSubmissions', $formSubmissions);
        $view->setVariable('query', []);
        $view->setVariable('count', null);

        return $view;
    }

    public function batchEditAllAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
        }

        // Derive the query, removing limiting and sorting params.
        $query = json_decode($this->params()->fromPost('query', []), true);
        unset($query['submit'], $query['page'], $query['per_page'], $query['limit'],
            $query['offset'], $query['sort_by'], $query['sort_order']);
        $count = $this->api()->search('formularium_form_submissions', ['limit' => 0] + $query)->getTotalResults();

        $form = $this->getForm(FormSubmissionBatchUpdateForm::class);
        $form->setAttribute('id', 'batch-edit-form-submission');
        if ($this->params()->fromPost('batch_update')) {
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                $job = $this->jobDispatcher()->dispatch('Omeka\Job\BatchUpdate', [
                    'resource' => 'formularium_form_submissions',
                    'query' => $query,
                    'data' => $data ?? [],
                ]);

                $this->messenger()->addSuccess('Editing form submissions. This may take a while.'); // @translate
                return $this->redirect()->toRoute(null, ['action' => 'browse'], ['query' => $this->params()->fromQuery()], true);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setTemplate('formularium/admin/form-submission/batch-edit');
        $view->setVariable('form', $form);
        $view->setVariable('formSubmissions', []);
        $view->setVariable('query', $query);
        $view->setVariable('count', $count);

        return $view;
    }
}
