<?php
namespace Formularium\Controller\Site;

use Formularium\Api\Representation\FormActionResultRepresentation;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Exception;

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
                $resource_id = $formData['formularium_resource_id'];
                unset($formData['formularium_site_page_block_id']);
                unset($formData['formularium_site_page_id']);
                unset($formData['csrf']);

                $data = [
                    'o:form' => ['o:id' => $formularium_form_id],
                    'o:site' => ['o:id' => $this->currentSite()->id()],
                    'o:site_page' => ['o:id' => $site_page_id],
                    'o:site_page_block' => ['o:id' => $site_page_block_id],
                    'o:resource' => ['o:id' => $resource_id],
                    'form_data' => $formData,
                ];

                $formSubmissionResponse = $this->api($form)->create('formularium_form_submissions', $data, $filesData);
                if ($formSubmissionResponse) {
                    $formSubmission = $formSubmissionResponse->getContent();

                    foreach ($formularium_form->actions() as $action) {
                        $formActionType = $this->formularium()->getFormActionType($action['type']);
                        $actionResultResponse = $this->api()->create('formularium_form_action_result', [
                            'o:form_submission' => ['o:id' => $formSubmission->id() ],
                            'o:action_label' => !empty($action['internal_label']) ? $action['internal_label'] : $formActionType->getLabel(),
                        ]);

                        $actionResultId =  $actionResultResponse->getContent()->id();
                        $actionResult = [];
                        try {
                            $actionResult = $formActionType->perform($action, $formSubmission, $formData);
                        } catch (Exception $e) {
                            $this->logger()->err($e);
                            $actionResult = [ 
                                'o:status' => FormActionResultRepresentation::ERROR,
                                'o:data' => [ 
                                    'reason' => 'Uncaught exception: ' . get_class($e),
                                    'message' =>  $e->getMessage(),
                                ],
                            ];
                        }

                        $this->api()->update('formularium_form_action_result', $actionResultId, $actionResult, ['isPartial' => true]);
                    }

                    $this->messenger()->addSuccess('Form sent successfully'); // @translate

                    if ($site_page_id) {
                        $page = $this->api()->read('site_pages', $site_page_id)->getContent();
                        return $this->redirect()->toUrl($page->url());
                    }

                    return $this->redirect()->toRoute('site/formularium/form-id', [
                        'id' => $formularium_form->id(),
                        'site-slug' => $this->currentSite()->slug()
                    ]);
                }
            }
        }

        $viewModel = new ViewModel([
            'form' => $form,
            'formulariumForm' => $formularium_form,
        ]);

        return $viewModel;
    }
}
