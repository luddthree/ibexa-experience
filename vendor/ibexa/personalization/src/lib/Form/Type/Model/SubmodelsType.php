<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Form\Mapper\SubmodelMapperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SubmodelsType extends AbstractType
{
    /** @var \Ibexa\Personalization\Form\Mapper\SubmodelMapperInterface */
    private $submodelMapper;

    public function __construct(
        SubmodelMapperInterface $submodelMapper
    ) {
        $this->submodelMapper = $submodelMapper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['submodels'] as $submodel) {
            $this->submodelMapper->mapSubmodelForm($builder, $submodel);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['submodels']);

        $resolver->setDefaults([
            'submodels' => [],
        ]);
    }
}

class_alias(SubmodelsType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\SubmodelsType');
