<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FormSubmission;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission;
use Ibexa\FormBuilder\FormSubmission\Notification\EmailNotificationSender;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Template;

class EmailNotificationSenderTest extends TestCase
{
    /** @var \Swift_Mailer|\PHPUnit\Framework\MockObject\MockObject */
    private $mailer;

    /** @var \Twig\Environment|\PHPUnit\Framework\MockObject\MockObject */
    private $twig;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(Swift_Mailer::class);
        $this->twig = $this->createMock(Environment::class);
    }

    public function testSendNotification(): void
    {
        $expectedSubject = 'Expected subject';
        $expectedBody = 'Expected body';
        $expectedRecipients = [
            'foo@email.com', 'bar@email.com', 'baz@email.com',
        ];

        $content = $this->createMock(Content::class);
        $form = $this->createMock(Form::class);
        $data = $this->createMock(FormSubmission::class);
        $field = $this->createMock(Field::class);

        $field
            ->expects($this->once())
            ->method('hasAttribute')
            ->with('notification_email')
            ->willReturn(true);

        $field
            ->expects($this->once())
            ->method('getAttributeValue')
            ->with('notification_email')
            ->willReturn(implode(', ', $expectedRecipients));

        $emailTemplate = $this->createMock(Template::class);
        $emailTemplateClass = 'My\Custom\Class';

        $this->twig
            ->expects($this->once())
            ->method('loadTemplate')
            ->with($emailTemplateClass, 'email_template.html.twig')
            ->willReturn($emailTemplate);

        $this->twig
            ->expects($this->once())
            ->method('getTemplateClass')
            ->with('email_template.html.twig')
            ->willReturn($emailTemplateClass);

        $parameters = [
            'content' => $content,
            'form' => $form,
            'data' => $data,
        ];

        $emailTemplate
            ->expects($this->at(0))
            ->method('renderBlock')
            ->with('subject', $parameters)
            ->willReturn($expectedSubject);

        $emailTemplate
            ->expects($this->at(1))
            ->method('renderBlock')
            ->with('body', $parameters)
            ->willReturn($expectedBody);

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function (Swift_Message $message) use ($expectedSubject, $expectedBody, $expectedRecipients) {
                $this->assertEquals($expectedSubject, $message->getSubject());
                $this->assertEquals($expectedBody, $message->getBody());
                $this->assertEquals($expectedRecipients, array_keys($message->getTo()));
            });

        $sender = new EmailNotificationSender($this->mailer, $this->twig, 'email_template.html.twig', 'noreply@domain.com');
        $sender->sendNotification($content, $form, $data, $field);
    }

    public function testSendNotificationMissingNotificationEmailAttribute(): void
    {
        $content = $this->createMock(Content::class);
        $form = $this->createMock(Form::class);
        $data = $this->createMock(FormSubmission::class);
        $field = $this->createMock(Field::class);

        $field
            ->expects($this->once())
            ->method('hasAttribute')
            ->with('notification_email')
            ->willReturn(false);

        $field
            ->expects($this->never())
            ->method('getAttributeValue');

        $this->twig
            ->expects($this->never())
            ->method('loadTemplate');

        $this->mailer
            ->expects($this->never())
            ->method('send');

        $sender = new EmailNotificationSender($this->mailer, $this->twig, 'email_template.html.twig', 'noreply@domain.com');
        $sender->sendNotification($content, $form, $data, $field);
    }

    public function testSendNotificationWithEmptyRecipientsList(): void
    {
        $content = $this->createMock(Content::class);
        $form = $this->createMock(Form::class);
        $data = $this->createMock(FormSubmission::class);
        $field = $this->createMock(Field::class);

        $field
            ->expects($this->once())
            ->method('hasAttribute')
            ->with('notification_email')
            ->willReturn(true);

        $field
            ->expects($this->once())
            ->method('getAttributeValue')
            ->with('notification_email')
            ->willReturn('');

        $this->twig
            ->expects($this->never())
            ->method('loadTemplate');

        $this->mailer
            ->expects($this->never())
            ->method('send');

        $sender = new EmailNotificationSender($this->mailer, $this->twig, 'email_template.html.twig', 'noreply@domain.com');
        $sender->sendNotification($content, $form, $data, $field);
    }
}

class_alias(EmailNotificationSenderTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FormSubmission\EmailNotificationSenderTest');
