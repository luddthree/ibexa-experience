<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EmailFieldType extends AbstractFieldType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EmailType::class;
    }
}

class_alias(EmailFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\EmailFieldType');
