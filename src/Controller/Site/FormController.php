<?php
namespace Formularium\Controller\Site;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class FormController extends AbstractActionController
{
    public function renderAction()
    {
        $formularium_form_id = $this->params()->fromRoute('id');
        $formularium_form = $this->api()->read('formularium_forms', $formularium_form_id)->getContent();
        $form = $this->getForm('Formularium\Form\FormulariumForm', ['formularium_form' => $formularium_form]);
        $form->setAttribute('action', $this->url()->fromRoute('site/formularium/form-id', ['id' => $formularium_form->id()], [], true));

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $filesData = $this->params()->fromFiles();
            $post = array_merge_recursive($postData, $filesData);
            $form->setData($post);
            if ($form->isValid()) {
                $formData = $form->getData();

                $site_page_block_id = $formData['formularium_site_page_block_id'];
                $site_page_id = $formData['formularium_site_page_id'];
                unset($formData['formularium_site_page_block_id']);
                unset($formData['formularium_site_page_id']);
                unset($formData['csrf']);

                $data = [
                    'o:form' => ['o:id' => $formularium_form_id],
                    'o:site' => ['o:id' => $this->currentSite()->id()],
                    'o:site_page' => ['o:id' => $site_page_id],
                    'o:site_page_block' => ['o:id' => $site_page_block_id],
                    'form_data' => $formData,
                ];

                $response = $this->api($form)->create('formularium_form_submissions', $data, $filesData);
                if ($response) {
                    $formSubmission = $response->getContent();

                    // TODO Perform actions
                    foreach ($formularium_form->actions() as $action) {
                        $formActionType = $this->formularium()->getFormActionType($action['type']);
                        $formActionType->perform($action, $formSubmission, $formData);
                    }

                    $this->messenger()->addSuccess('Form sent sucessfully');

                    if ($site_page_id) {
                        $page = $this->api()->read('site_pages', $site_page_id)->getContent();

                        return $this->redirect()->toUrl($page->url());
                    }

                    return $this->redirect()->toRoute('site/formularium/form-id', ['id' => $formularium_form->id(), 'site-slug' => $this->currentSite()->slug()]);
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $viewModel = new ViewModel([
            'form' => $form,
            'formulariumForm' => $formularium_form,
        ]);

        return $viewModel;
    }
}
