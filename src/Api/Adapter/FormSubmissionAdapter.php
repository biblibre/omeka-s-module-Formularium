<?php
namespace Formularium\Api\Adapter;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Formularium\FormComponent\FormComponent;
use Formularium\Entity\FormulariumFormSubmissionFile;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class FormSubmissionAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'submitted' => 'submitted',
        'handled' => 'handled',
    ];

    protected $scalarFields = [
        'id' => 'id',
        'submitted' => 'submitted',
        'handled' => 'handled',
    ];

    public function getEntityClass()
    {
        return 'Formularium\Entity\FormulariumFormSubmission';
    }

    public function getResourceName()
    {
        return 'formularium_form_submissions';
    }

    public function getRepresentationClass()
    {
        return 'Formularium\Api\Representation\FormSubmissionRepresentation';
    }

    public function buildQuery(QueryBuilder $qb, array $query): void
    {
        if (!empty($query['form_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.form',
                $this->createNamedParameter($qb, $query['form_id']))
            );
        }

        if (!empty($query['site_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.site',
                $this->createNamedParameter($qb, $query['site_id']))
            );
        }

        if (!empty($query['site_page_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.sitePage',
                $this->createNamedParameter($qb, $query['site_page_id']))
            );
        }

        if (!empty($query['site_page_block_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.sitePageBlock',
                $this->createNamedParameter($qb, $query['site_page_block_id']))
            );
        }

        if (!empty($query['submitter_id'])) {
            $qb->andWhere($qb->expr()->eq('omeka_root.submitter', $query['submitter_id']));
        }

        if (isset($query['handled']) && $query['handled'] !== '') {
            if ($query['handled']) {
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.handled'));
            } else {
                $qb->andWhere($qb->expr()->isNull('omeka_root.handled'));
            }
        }

        if (!empty($query['handler_id'])) {
            $qb->andWhere($qb->expr()->eq('omeka_root.handler', $query['handler_id']));
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore): void
    {
        /** @var \Formularium\Entity\FormulariumFormSubmission $entity */

        $data = $request->getContent();
        $serviceLocator = $this->getServiceLocator();
        $user = $serviceLocator->get('Omeka\AuthenticationService')->getIdentity();
        $formComponentTypeManager = $serviceLocator->get('Formularium\FormComponentTypeManager');

        if (Request::CREATE === $request->getOperation()) {
            if (!empty($data['o:form']['o:id'])) {
                $formulariumForm = $this->getAdapter('formularium_forms')->findEntity($data['o:form']['o:id']);
                $entity->setForm($formulariumForm);
            }

            if (!empty($data['o:site']['o:id'])) {
                $site = $this->getAdapter('sites')->findEntity($data['o:site']['o:id']);
                $entity->setSite($site);
            }

            if (!empty($data['o:site_page']['o:id'])) {
                $sitePage = $this->getAdapter('site_pages')->findEntity($data['o:site_page']['o:id']);
                $entity->setSitePage($sitePage);
            }

            if (!empty($data['o:site_page_block']['o:id'])) {
                $sitePageBlock = $this->getEntityManager()->find('Omeka\Entity\SitePageBlock', $data['o:site_page_block']['o:id']);
                $entity->setSitePageBlock($sitePageBlock);
            }

            $entity->setSubmitter($user);
            $entity->setSubmitted(new DateTime);
            $entity->setData([]);

            $components = $formulariumForm->getComponents();
            foreach ($components as $component) {
                $formComponentType = $formComponentTypeManager->get($component['type']);
                $formComponentType->hydrateFormSubmission(new FormComponent($component), $request, $entity, $errorStore);
            }
        }

        if (Request::UPDATE === $request->getOperation() && $this->shouldHydrate($request, 'o:handled')) {
            $this->authorize($entity, 'update');
            $handled = $request->getValue('o:handled');
            if ($handled) {
                $entity->setHandled(new DateTime);
                $entity->setHandler($user);
            } else {
                $entity->setHandled(null);
                $entity->setHandler(null);
            }
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        /** @var \Formularium\Entity\Form $entity */


        // TODO Validate components
        // TODO Validate actions
    }
}
