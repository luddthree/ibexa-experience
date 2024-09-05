<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class DateBasedPublisherType extends AbstractType
{
    public const VALIDATION_GROUP = 'date_based_publisher';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('timestamp', HiddenType::class);
    }
}

class_alias(DateBasedPublisherType::class, 'EzSystems\DateBasedPublisher\Core\Form\Type\DateBasedPublisherType');
