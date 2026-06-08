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
        'action_label' => 'action_label',
    ];

    protected $scalarFields = [
        'id' => 'id',
        'action_label' => 'action_label',
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
        if (isset($query['form_submission_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.formSubmission',
                $this->createNamedParameter($qb, $query['form_submission_id']),
            ));
        }

        if (isset($query['status'])) {
            $qb->andWhere($qb->expr()->in(
                'omeka_root.status',
                $this->createNamedParameter($qb, $query['status']),
            ));
        }
    }

    public function validateRequest(Request $request, ErrorStore $errorStore)
    {
        $data = $request->getContent();

        switch ($request->getOperation()) {
            case Request::CREATE:
                if (!array_key_exists('o:status', $data)) {
                    return;
                } // nothing to do
                if (!in_array($data['o:status'], FormActionResultRepresentation::STATUSES)) {
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
        switch ($request->getOperation()) {
            case Request::CREATE:
                if (!empty($data['o:form_submission']['o:id'])) {
                    $formSubmission = $this
                        ->getAdapter('formularium_form_submissions')
                        ->findEntity($data['o:form_submission']['o:id']);

                    $entity->setFormSubmission($formSubmission);
                }

                if (!empty($data['o:action_label'])) {
                    $entity->setActionLabel($data['o:action_label']);
                }

                $entity->setStatus('created');
                $entity->setData([]);

                break;

            case Request::UPDATE:
                if ($this->shouldHydrate($request, 'o:status')) {
                    $status = $request->getValue('o:status');
                    $entity->setStatus($status);
                }
                if ($this->shouldHydrate($request, 'o:data')) {
                    $data = $request->getValue('o:data');
                    $entity->setData($data);
                }
                break;
            default:
                break;
        }
    }

}
