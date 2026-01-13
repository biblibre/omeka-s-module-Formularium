<?php

namespace Formularium;

use Omeka\Module\AbstractModule;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\EventManager\Event;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $services = $this->getServiceLocator();
        $acl = $services->get('Omeka\Acl');
        $acl->allow(null, 'Formularium\Controller\Site\Form');
        $acl->allow(null, 'Formularium\Api\Adapter\FormAdapter', 'read');
        $acl->allow(null, 'Formularium\Api\Adapter\FormSubmissionAdapter', 'create');
        $acl->allow(null, 'Formularium\Entity\FormulariumFormSubmission', 'create');
        $acl->allow(null, 'Formularium\Entity\FormulariumForm', 'read');
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');

        $connection->executeStatement(<<<'SQL'
        CREATE TABLE formularium_form (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            components LONGTEXT NOT NULL COMMENT '(DC2Type:json)',
            actions LONGTEXT NOT NULL COMMENT '(DC2Type:json)',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $connection->executeStatement(<<<'SQL'
        CREATE TABLE formularium_form_submission (
            id INT AUTO_INCREMENT NOT NULL,
            form_id INT NOT NULL,
            site_id INT DEFAULT NULL,
            site_page_id INT DEFAULT NULL,
            site_page_block_id INT DEFAULT NULL,
            submitter_id INT DEFAULT NULL,
            handler_id INT DEFAULT NULL,
            submitted DATETIME NOT NULL,
            handled DATETIME DEFAULT NULL,
            data LONGTEXT NOT NULL COMMENT '(DC2Type:json)',
            INDEX IDX_26C0C2F45FF69B7D (form_id),
            INDEX IDX_26C0C2F4F6BD1646 (site_id),
            INDEX IDX_26C0C2F4F7E2812F (site_page_id),
            INDEX IDX_26C0C2F43BD1ED87 (site_page_block_id),
            INDEX IDX_26C0C2F4919E5513 (submitter_id),
            INDEX IDX_26C0C2F4A6E82043 (handler_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $connection->executeStatement(<<<'SQL'
        CREATE TABLE formularium_form_submission_file (
            id INT AUTO_INCREMENT NOT NULL,
            form_submission_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            media_type VARCHAR(255) NOT NULL,
            storage_id VARCHAR(190) NOT NULL,
            extension VARCHAR(255) DEFAULT NULL,
            UNIQUE INDEX UNIQ_E23B837F5CC5DB90 (storage_id),
            INDEX IDX_E23B837F422B0E0C (form_submission_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $connection->executeStatement(<<<'SQL'
        ALTER TABLE formularium_form_submission
        ADD CONSTRAINT FK_26C0C2F45FF69B7D
        FOREIGN KEY (form_id) REFERENCES formularium_form (id)
        ON DELETE CASCADE
        SQL);

        $connection->executeStatement(<<<'SQL'
        ALTER TABLE formularium_form_submission
        ADD CONSTRAINT FK_26C0C2F4F6BD1646
        FOREIGN KEY (site_id) REFERENCES site (id)
        ON DELETE SET NULL
        SQL);

        $connection->executeStatement(<<<'SQL'
        ALTER TABLE formularium_form_submission
        ADD CONSTRAINT FK_26C0C2F4F7E2812F
        FOREIGN KEY (site_page_id) REFERENCES site_page (id)
        ON DELETE SET NULL
        SQL);

        $connection->executeStatement(<<<'SQL'
        ALTER TABLE formularium_form_submission
        ADD CONSTRAINT FK_26C0C2F43BD1ED87 FOREIGN KEY (site_page_block_id)
        REFERENCES site_page_block (id)
        ON DELETE SET NULL
        SQL);

        $connection->executeStatement(<<<'SQL'
        ALTER TABLE formularium_form_submission
        ADD CONSTRAINT FK_26C0C2F4919E5513
        FOREIGN KEY (submitter_id) REFERENCES user (id)
        ON DELETE SET NULL
        SQL);

        $connection->executeStatement(<<<'SQL'
        ALTER TABLE formularium_form_submission
        ADD CONSTRAINT FK_26C0C2F4A6E82043
        FOREIGN KEY (handler_id) REFERENCES user (id)
        ON DELETE SET NULL
        SQL);

        $connection->executeStatement(<<<'SQL'
        ALTER TABLE formularium_form_submission_file
        ADD CONSTRAINT FK_E23B837F422B0E0C
        FOREIGN KEY (form_submission_id) REFERENCES formularium_form_submission (id)
        ON DELETE CASCADE
        SQL);
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');

        // TODO Delete files from storage
        $connection->executeStatement('DROP TABLE formularium_form_submission_file');
        $connection->executeStatement('DROP TABLE formularium_form_submission');
        $connection->executeStatement('DROP TABLE formularium_form');
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Formularium\Controller\Admin\FormSubmission',
            'view.search.filters',
            [$this, 'onFormSubmissionViewSearchFilters']
        );

        $sharedEventManager->attach(
            'Formularium\Entity\FormulariumFormSubmissionFile',
            'entity.remove.post',
            [$this, 'onFormSubmissionFileRemove']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Admin\Index',
            'view.browse.after',
            [$this, 'onAdminIndexViewBrowseAfter']
        );
    }

    public function getConfig()
    {
        return require __DIR__ . '/config/module.config.php';
    }

    public function onFormSubmissionViewSearchFilters(Event $event)
    {
        $view = $event->getTarget();
        $filters = $event->getParam('filters', []);
        $query = $event->getParam('query', []);

        if (!empty($query['form_id'])) {
            $formulariumForm = $view->api()->read('formularium_forms', $query['form_id'])->getContent();
            if ($formulariumForm) {
                $filters[$view->translate('Form')][] = $formulariumForm->name();
            }
        }

        if (!empty($query['site_id'])) {
            $site = $view->api()->read('sites', $query['site_id'])->getContent();
            if ($site) {
                $filters[$view->translate('Site')][] = $site->title();
            }
        }

        if (!empty($query['site_page_id'])) {
            $sitePage = $view->api()->read('site_pages', $query['site_page_id'])->getContent();
            if ($sitePage) {
                $filters[$view->translate('Page')][] = $sitePage->title();
            }
        }

        if (!empty($query['site_page_block_id'])) {
            $filters[$view->translate('Page block ID')][] = $query['site_page_block_id'];
        }

        if (isset($query['submitter_id']) && $query['submitter_id']) {
            $user = $view->api()->read('users', $query['submitter_id'])->getContent();
            if ($user) {
                $filters[$view->translate('Submitted by')][] = $user->name();
            }
        }

        if (isset($query['handled']) && $query['handled'] !== '') {
            $filters[$view->translate('Handled')][] = $query['handled'] ? $view->translate('Yes') : $view->translate('No');
        }

        if (isset($query['handler_id']) && $query['handler_id']) {
            $user = $view->api()->read('users', $query['handler_id'])->getContent();
            if ($user) {
                $filters[$view->translate('Handled by')][] = $user->name();
            }
        }

        $event->setParam('filters', $filters);
    }

    public function onFormSubmissionFileRemove(Event $event)
    {
        $formSubmissionFile = $event->getTarget();
        $store = $this->getServiceLocator()->get('Omeka\File\Store');

        $storagePath = sprintf('formularium/%s', $formSubmissionFile->getFilename());
        $store->delete($storagePath);
    }

    public function onAdminIndexViewBrowseAfter(Event $event)
    {
        $view = $event->getTarget();

        if (!$view->userIsAllowed('Formularium\Controller\Admin\FormSubmission', 'browse')) {
            return;
        }

        echo $view->partial('formularium/common/dashboard');
    }
}
