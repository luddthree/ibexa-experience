<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Form\DataTransformer\SubmodelTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class SubmodelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source', HiddenType::class);

        $builder->addModelTransformer(new SubmodelTransformer());
    }
}

class_alias(SubmodelType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\SubmodelType');
