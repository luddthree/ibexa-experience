<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class NominalValueEntryType extends AbstractType
{
    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'personalization_attribute_nominal_value_entry';
    }
}

class_alias(NominalValueEntryType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\NominalValueEntryType');
