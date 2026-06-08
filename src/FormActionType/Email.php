<?php

namespace Formularium\FormActionType;

use Formularium\Api\Representation\FormSubmissionRepresentation;
use Formularium\Api\Representation\FormActionResultRepresentation;
use Laminas\Form\Fieldset;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Laminas\Log\Logger;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Stdlib\Mailer;

class Email extends AbstractFormActionType
{
    public function __construct(
        protected Mailer $mailer,
        protected TranslatorInterface $translator,
        protected Logger $logger
    ) {
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
                'info' => 'This field can contain special tokens that will be automatically replaced', // @translate
                'documentation' => 'https://biblibre.github.io/omeka-s-module-Formularium/en/form-actions.html#send-an-email',
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
                'info' => 'This field can contain special tokens that will be automatically replaced', // @translate
                'documentation' => 'https://biblibre.github.io/omeka-s-module-Formularium/en/form-actions.html#send-an-email',
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
                'info' => 'This field can contain special tokens that will be automatically replaced', // @translate
                'documentation' => 'https://biblibre.github.io/omeka-s-module-Formularium/en/form-actions.html#send-an-email',
            ],
            'attributes' => [
                'rows' => '4',
                'required' => true,
            ],
        ]);
    }

    public function perform(
        array $action,
        FormSubmissionRepresentation $formSubmission,
        array $data,
    ): array {
        $values = $formSubmission->data();

        if ($resource = $formSubmission->resource()) {
            $values['formularium:resource:id'] = $resource->id();
            $values['formularium:resource:title'] = $resource->displayTitle();
            $values['formularium:resource:type'] = $this->getResourceType($resource);

            if ($site = $formSubmission->site()) {
                $values['formularium:resource:url'] = $resource->siteUrl($site->slug(), true);
            }
        }

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

        try {
            $this->mailer->send($message);
        } catch (MailException $e) {
            $this->logger->err((string) $e);
            return [
                'o:status' => FormActionResultRepresentation::FAILED,
                'o:data' => [
                    'message' => 'Could not send mail',
                    'reason' => $e->getMessage(),
                ],
            ];
        }

        return [
            'o:status' => FormActionResultRepresentation::SUCCEEDED, 
            'o:data' => [
                'message' => 'Mail sent',
            ],
        ];
    }

    protected function renderTemplate(string $template, array $values): string
    {
        $output = preg_replace_callback(
            '/\{(.+?)\}/',
            function ($matches) use ($values) {
                $key = $matches[1];

                if (array_key_exists($key, $values)) {
                    $value = $values[$key];
                    if (is_scalar($value) || $value instanceof \Stringable) {
                        return (string) $value;
                    }

                    return '';
                }

                return $matches[0];
            },
            $template
        );

        return $output;
    }

    protected function getResourceType(AbstractResourceEntityRepresentation $resource): string
    {
        $type = match($resource->resourceName()) {
            'item_sets' => $this->translator->translate('item set'),
            'items' => $this->translator->translate('item'),
            'media' => $this->translator->translate('media'),
        };

        return $type;
    }
}
