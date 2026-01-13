<?php

namespace Formularium\FormActionType;

use Formularium\FormAction\FormAction;
use Formularium\Api\Representation\FormSubmissionRepresentation;
use Laminas\Form\Fieldset;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Stdlib\ErrorStore;
use Omeka\Stdlib\Mailer;

class Email extends AbstractFormActionType
{
    public function __construct(protected Mailer $mailer)
    {
    }

    public function getLabel(): string
    {
        return 'Send an email'; // @translate
    }

    public function settingsFieldsetAddElements(Fieldset $fieldset): void
    {
        $fieldset->add([
            'name' => 'to',
            'type' => 'Laminas\Form\Element\Email',
            'options' => [
                'label' => 'To', // @translate
            ],
        ]);

        $fieldset->add([
            'name' => 'subject',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Subject', // @translate
            ],
        ]);

        $fieldset->add([
            'name' => 'body',
            'type' => 'Laminas\Form\Element\Textarea',
            'options' => [
                'label' => 'Body', // @translate
            ],
            'attributes' => [
                'id' => 'action-email-' . rand(),
            ],
        ]);
    }

    public function perform(array $action, FormSubmissionRepresentation $formSubmission, array $data): void
    {
        $message = $this->mailer->createMessage([
            'to' => $action['settings']['to'],
            'subject' => $action['settings']['subject'],
            'body' => $action['settings']['body'],
        ]);
        $this->mailer->send($message);
    }
}
