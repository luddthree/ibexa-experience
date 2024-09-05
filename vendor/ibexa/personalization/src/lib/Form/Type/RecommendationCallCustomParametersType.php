<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\DataTransformer\RecommendationCallCustomParametersTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class RecommendationCallCustomParametersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new RecommendationCallCustomParametersTransformer()
        );
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}

class_alias(RecommendationCallCustomParametersType::class, 'Ibexa\Platform\Personalization\Form\Type\RecommendationCallCustomParametersType');
