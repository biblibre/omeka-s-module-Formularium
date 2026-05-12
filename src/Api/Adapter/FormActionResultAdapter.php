<?php

namespace Formularium\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;
use Formularium\Api\Representation\FormActionResultRepresentation;

class FormActionResultAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'action_internal_label' => 'action_internal_label',
    ];

    protected $scalarFields = [
        'id' => 'id',
        'action_internal_label' => 'action_internal_label',
    ];

    public function getEntityClass()
    {
        return 'Formularium\Entity\FormulariumFormActionResult';
    } 

    public function getResourceName()
    {
        return 'formularium_form_action_result';
    }

    public function getRepresentationClass()
    {
        return 'Formularium\Api\Representation\FormActionResultRepresentation';
    }

    public function validateRequest(Request $request, ErrorStore $errorStore) 
    {
        $data = $request->getContent();

        switch ($request->getOperation()) 
        {
            case Request::CREATE:
                if (!array_key_exists('o:status', $data))
                    return; // nothing to do
                if (!in_array($data['o:status'],  FormActionResultRepresentation::STATUSES)) {
                    $errorStore->addError("o:status", "Status is invalid. Must be one of: 'created', 'succeeded' or 'failed'");
                }
                break;
            case Request::UPDATE:
                $status = $data['o:status'] ?? FormActionResultRepresentation::CREATED;
                if ($status === FormActionResultRepresentation::CREATED) {
                    $errorStore->addError("o:status", "The status should have been updated.");
                }
                break;
            default:
                break;
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore): void
    {
        /** @var \Formularium\Entity\FormulariumFormActionResult $entity */
        $data = $request->getContent();
        $serviceLocator = $this->getServiceLocator();
        $formComponentTypeManager = $serviceLocator->get('Formularium\FormComponentTypeManager');
        switch ($request->getOperation())
        {
            case Request::CREATE:
                $entity->setActionInternalLabel($request->getValue('o:action_internal_label'));

                if (!empty($data['o:form_submission']['o:id'])) {
                    $formSubmission = $this
                        ->getAdapter('formularium_form_submissions')
                        ->findEntity($data['o:form_submission']['o:id']);

                    $entity->setFormSubmission($formSubmission);
                }

                $entity->setStatus('created');
                $entity->setData([]);
                break;

            case Request::UPDATE:
                $this->authorize($entity, 'update');

                $status = $request->getValue('o:status');
                $data = $request->getValue('o:data');

                $entity->setStatus($status);
                $entity->setData($data);
                break;
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        /** @var \Formularium\Entity\FormulariumFormActionResult $entity */

        // TODO Validate components
        // TODO Validate actions
    }

    public function preprocessBatchUpdate(array $data, Request $request)
    {
    }
}
