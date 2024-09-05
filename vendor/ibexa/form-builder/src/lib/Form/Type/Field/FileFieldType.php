<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class FileFieldType extends AbstractFieldType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return FileType::class;
    }
}

class_alias(FileFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\FileFieldType');
