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
                'rows' => '4',
            ],
        ]);
    }

    public function perform(array $action, FormSubmissionRepresentation $formSubmission, array $data): void
    {
        $values = $formSubmission->data();

        $to = $this->renderTemplate($action['settings']['to'], $values);
        $subject = $this->renderTemplate($action['settings']['subject'], $values);
        $body = $this->renderTemplate($action['settings']['body'], $values);

        $message = $this->mailer->createMessage([
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
        ]);

        $this->mailer->send($message);
    }

    protected function renderTemplate(string $template, array $values): string
    {
        $output = preg_replace_callback(
            '/\{(.+?)\}/',
            function ($matches) use ($values) {
                $key = $matches[1];
                $value = $values[$key] ?? null;

                return is_string($value) ? $value : $matches[0];
            },
            $template
        );

        return $output;
    }
}
