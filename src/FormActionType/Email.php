<?php

namespace Formularium\FormActionType;

use Formularium\Api\Representation\FormSubmissionRepresentation;
use Laminas\Form\Fieldset;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
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
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'To', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $fieldset->add([
            'name' => 'subject',
            'type' => 'Laminas\Form\Element\Text',
            'options' => [
                'label' => 'Subject', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $fieldset->add([
            'name' => 'body',
            'type' => 'Laminas\Form\Element\Textarea',
            'options' => [
                'label' => 'Body', // @translate
            ],
            'attributes' => [
                'rows' => '4',
                'required' => true,
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
        ]);

        $text = new MimePart($body);
        $text->type = Mime::TYPE_TEXT;
        $text->charset = 'UTF-8';
        $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($text);
        $message->setBody($mimeMessage);

        $message->setEncoding('UTF-8');
        $message->getHeaders()->addHeaderLine('Content-Type', 'text/plain; charset=UTF-8');

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
