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

interface NotificationSenderInterface
{
    /**
     * Send a notification about form submission.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission $data
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     */
    public function sendNotification(Content $content, Form $form, FormSubmission $data, Field $field): void;
}

class_alias(NotificationSenderInterface::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Notification\NotificationSenderInterface');
