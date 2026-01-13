<?php
namespace Formularium\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Formularium\FormAction\FormAction;
use Formularium\FormComponent\FormComponent;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class FormAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'name' => 'name',
    ];

    protected $scalarFields = [
        'id' => 'id',
        'name' => 'name',
    ];

    public function getEntityClass()
    {
        return 'Formularium\Entity\FormulariumForm';
    }

    public function getResourceName()
    {
        return 'formularium_forms';
    }

    public function getRepresentationClass()
    {
        return 'Formularium\Api\Representation\FormRepresentation';
    }

    public function buildQuery(QueryBuilder $qb, array $query): void
    {
        if (isset($query['name'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.name',
                $this->createNamedParameter($qb, $query['name']))
            );
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore): void
    {
        /** @var \Formularium\Entity\FormulariumForm $entity */

        $services = $this->getServiceLocator();
        $formComponentTypeManager = $services->get('Formularium\FormComponentTypeManager');
        $formActionTypeManager = $services->get('Formularium\FormActionTypeManager');

        if ($this->shouldHydrate($request, 'o:name')) {
            $entity->setName($request->getValue('o:name'));
        }

        if ($this->shouldHydrate($request, 'o:components')) {
            $components = $request->getValue('o:components', []);
            if (!is_array($components)) {
                $components = [];
            }

            $formComponents = [];
            foreach ($components as $component) {
                $formComponentType = $formComponentTypeManager->get($component['type']);
                $formComponent = new FormComponent($component);
                $formComponentType->hydrateFormComponent($formComponent, $errorStore);
                $formComponents[] = $formComponent->jsonSerialize();
            }

            $entity->setComponents($formComponents);
        }

        if ($this->shouldHydrate($request, 'o:actions')) {
            $actions = $request->getValue('o:actions', []);
            if (!is_array($actions)) {
                $actions = [];
            }

            $formActions = [];
            foreach ($actions as $action) {
                $formActionType = $formActionTypeManager->get($action['type']);
                $formAction = new FormAction($action);
                $formActionType->hydrateFormAction($formAction, $errorStore);
                $formActions[] = $formAction->jsonSerialize();
            }

            $entity->setActions($formActions);
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        /** @var \Formularium\Entity\Form $entity */

        $name = $entity->getName();
        if (!is_string($name) || $name === '') {
            $errorStore->addError('o:name', 'A form must have a name.');
        }

        // TODO Validate components
        // TODO Validate actions
    }
}
