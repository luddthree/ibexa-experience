<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class InstallationKeyType extends AbstractType
{
    public function getParent(): string
    {
        return TextType::class;
    }
}

class_alias(InstallationKeyType::class, 'Ibexa\Platform\Personalization\Form\Type\InstallationKeyType');
