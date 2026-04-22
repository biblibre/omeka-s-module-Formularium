<?php

namespace Formularium\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

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


    public function buildQuery(QueryBuilder $qb, array $query): void
    {
        if (!empty($query['action_internal_label'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.action_internal_label',
                $this->createNamedParameter($qb, $query['action_internal_label']))
            );
        }

        if (!empty($query['status'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.status',
                $this->createNamedParameter($qb, $query['status']))
            );
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore): void
    {
        /** @var \Formularium\Entity\FormulariumFormActionResult $entity */
        $data = $request->getContent();
        $serviceLocator = $this->getServiceLocator();
        $formComponentTypeManager = $serviceLocator->get('Formularium\FormComponentTypeManager');
        if (Request::CREATE === $request->getOperation()) {

            if ($this->shouldHydrate($request, 'o:action_internal_label')) {
                $entity->setActionInternalLabel($request->getValue('o:action_internal_label'));
            }

            if (!empty($data['o:from_submission']['o:id'])) {
                $formSubmission = $this
                    ->getAdapter('formularium_form_submissions')
                    ->findEntity($data['o:from_submission']['o:id']);

                $entity->setFormSubmission($formSubmission);
            }

            $entity->setStatus('created');
            $entity->setData([]);
        }

        if (Request::UPDATE === $request->getOperation()) {
            $this->authorize($entity, 'update');

            $status = $request->getValue('o:status');
            $data = $request->getValue('o:data');

            $entity->setStatus($status);
            $entity->setData($data);
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
