<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Mapper;

use Ibexa\Personalization\Form\Data\SubmodelData;
use Ibexa\Personalization\Form\Type\Model\SubmodelNominalType;
use Ibexa\Personalization\Form\Type\Model\SubmodelNumericType;
use Ibexa\Personalization\Value\Model\Attribute;
use Ibexa\Personalization\Value\Model\Submodel;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

final class SubmodelMapper implements SubmodelMapperInterface
{
    public function mapSubmodelForm(
        FormBuilderInterface $builder,
        SubmodelData $submodel
    ): void {
        // @todo: TBD, replace this with registry and/or strategy
        if ($submodel->getType() === Attribute::TYPE_NOMINAL) {
            $this->mapNominalType($builder, $submodel);
        } elseif ($submodel->getType() === Attribute::TYPE_NUMERIC) {
            $this->mapNumericType($builder, $submodel);
        } else {
            throw new RuntimeException('Not supported');
        }
    }

    private function mapNominalType(
        FormBuilderInterface $builder,
        Submodel $submodel
    ): void {
        $builder->add(
            $submodel->getAttributeKey(),
            SubmodelNominalType::class,
            [
                'data_class' => SubmodelData::class,
            ]
        );
    }

    private function mapNumericType(
        FormBuilderInterface $builder,
        Submodel $submodel
    ): void {
        $builder->add(
            $submodel->getAttributeKey(),
            SubmodelNumericType::class,
            [
                'data_class' => SubmodelData::class,
            ]
        );
    }
}

class_alias(SubmodelMapper::class, 'Ibexa\Platform\Personalization\Form\Mapper\SubmodelMapper');
