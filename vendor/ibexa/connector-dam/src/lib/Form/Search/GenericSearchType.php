<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class GenericSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'query',
                SearchType::class,
                [
                    'attr' => [
                        'data-main-dam-search-target' => true,
                        'hidden' => true,
                    ],
                    'label' => false,
                ]
            );
    }
}

class_alias(GenericSearchType::class, 'Ibexa\Platform\Connector\Dam\Form\Search\GenericSearchType');
