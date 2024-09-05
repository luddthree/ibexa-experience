<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType as BaseTextType;

final class TextType extends BaseAddressType
{
    public function getParent(): string
    {
        return BaseTextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_address_text';
    }
}
