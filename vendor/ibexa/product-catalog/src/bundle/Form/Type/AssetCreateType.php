<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\AssetCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssetCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $uris = $builder->create('uris', HiddenType::class);
        $uris->addModelTransformer(new StringToArrayTransformer());

        $tags = $builder->create('tags', HiddenType::class, [
            'required' => false,
        ]);
        $tags->addModelTransformer(new StringToArrayTransformer());
        $tags->addModelTransformer(
            new CallbackTransformer(
                static function ($values): ?array {
                    if ($values === null) {
                        return null;
                    }

                    if (!is_array($values)) {
                        throw new TransformationFailedException(
                            sprintf(
                                'Invalid data, expected an array value, received %s.',
                                get_debug_type($values)
                            )
                        );
                    }

                    $pairs = [];
                    foreach ($values as $identifier => $value) {
                        $pairs[] = "$identifier:$value";
                    }

                    return $pairs;
                },
                static function ($value): ?array {
                    if ($value === null) {
                        return null;
                    }

                    if (!is_array($value)) {
                        throw new TransformationFailedException(
                            sprintf(
                                'Invalid data, expected an array value, received %s.',
                                get_debug_type($value)
                            )
                        );
                    }

                    $values = [];
                    foreach ($value as $pair) {
                        list($identifier, $value) = explode(':', $pair, 2);
                        $values[$identifier] = $value;
                    }

                    return $values;
                }
            )
        );

        $builder->add($uris);
        $builder->add($tags);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssetCreateData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
