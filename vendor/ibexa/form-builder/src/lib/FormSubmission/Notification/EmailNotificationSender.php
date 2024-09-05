<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Notification;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class EmailNotificationSender implements NotificationSenderInterface
{
    private const NOTIFICATION_RECIPIENTS_ATTR = 'notification_email';
    private const NOTIFICATION_RECIPIENTS_SEPARATOR = ',';

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var \Twig\Environment */
    private $twig;

    /** @var string */
    private $emailTemplate;

    /** @var string */
    private $senderAddress;

    /**
     * @param \Swift_Mailer $mailer
     * @param \Twig\Environment $twig
     * @param string $emailTemplate
     * @param string $senderAddress
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig, string $emailTemplate, string $senderAddress = '')
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailTemplate = $emailTemplate;
        $this->senderAddress = $senderAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function sendNotification(Content $content, Form $form, FormSubmission $data, Field $field): void
    {
        if (!$field->hasAttribute(self::NOTIFICATION_RECIPIENTS_ATTR)) {
            return;
        }

        $recipients = (string) $field->getAttributeValue(self::NOTIFICATION_RECIPIENTS_ATTR);
        $recipients = explode(self::NOTIFICATION_RECIPIENTS_SEPARATOR, $recipients);
        $recipients = array_map('trim', $recipients);
        $recipients = array_filter($recipients, static function ($recipient) {
            return $recipient !== '';
        });

        if (empty($recipients)) {
            return;
        }

        $this->doSendEmail($recipients, [
            'content' => $content,
            'form' => $form,
            'data' => $data,
        ]);
    }

    /**
     * @param string[] $recipients
     * @param string[] $parameters
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function doSendEmail(array $recipients, array $parameters = []): void
    {
        $template = $this->twig->loadTemplate($this->twig->getTemplateClass($this->emailTemplate), $this->emailTemplate);

        $message = new Swift_Message();
        $message->setSubject($template->renderBlock('subject', $parameters));
        $message->setTo($recipients);
        $message->setBody($template->renderBlock('body', $parameters), 'text/html');

        if (empty($this->senderAddress) === false) {
            $message->setFrom($this->senderAddress);
        }

        $this->mailer->send($message);
    }
}

class_alias(EmailNotificationSender::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Notification\EmailNotificationSender');
