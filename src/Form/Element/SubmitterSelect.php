<?php
namespace Formularium\Form\Element;

use Doctrine\DBAL\Connection;
use Laminas\Form\Element\Select;
use Laminas\InputFilter\InputProviderInterface;
use Omeka\Api\Manager as ApiManager;

class SubmitterSelect extends Select
{
    protected ApiManager $apiManager;
    protected Connection $connection;

    public function getValueOptions(): array
    {
        $user_ids = $this->connection->fetchFirstColumn('SELECT DISTINCT submitter_id FROM formularium_form_submission');
        $users = $this->apiManager->search('users', ['id' => $user_ids, 'sort_by' => 'name'])->getContent();

        $valueOptions = [];
        foreach ($users as $user) {
            $valueOptions[$user->id()] = sprintf('%s (%s)', $user->name(), $user->email());
        }

        return $valueOptions;
    }

    public function setApiManager(ApiManager $apiManager): void
    {
        $this->apiManager = $apiManager;
    }

    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }
}
