<?php
namespace Formularium\Api\Adapter;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Formularium\Entity\FormulariumFormSubmissionFile;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class FormSubmissionFileAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'name' => 'name',
        'media_type' => 'mediaType',
        'storage_id' => 'storageId',
        'extension' => 'extension',
    ];

    protected $scalarFields = [
        'id' => 'id',
        'name' => 'name',
        'media_type' => 'mediaType',
        'storage_id' => 'storageId',
        'extension' => 'extension',
    ];

    public function getEntityClass()
    {
        return 'Formularium\Entity\FormulariumFormSubmissionFile';
    }

    public function getResourceName()
    {
        return 'formularium_form_submission_files';
    }

    public function getRepresentationClass()
    {
        return 'Formularium\Api\Representation\FormSubmissionFileRepresentation';
    }

    public function buildQuery(QueryBuilder $qb, array $query): void
    {
        if (isset($query['form_submission_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.formSubmission',
                $this->createNamedParameter($qb, $query['form_submission_id']))
            );
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore): void
    {
        /** @var \Formularium\Entity\FormulariumFormSubmissionFile $entity */

        $data = $request->getContent();
        $fileData = $request->getFileData();

        if (Request::CREATE === $request->getOperation()) {
            $uploader = $this->getServiceLocator()->get('Omeka\File\Uploader');
            $tempFile = $uploader->upload($fileData['file'], $errorStore);
            if (!$tempFile) {
                return;
            }

            $tempFile->setSourceName($fileData['file']['name']);

            $entity->setStorageId($tempFile->getStorageId());
            $entity->setExtension($tempFile->getExtension());
            $entity->setMediaType($tempFile->getMediaType());
            $entity->setName($fileData['name']);

            $tempFile->store('formularium');
            $tempFile->delete();
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
    }
}
