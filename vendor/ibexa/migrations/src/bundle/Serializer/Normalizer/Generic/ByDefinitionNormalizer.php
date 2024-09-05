<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Generic;

use function get_class;
use function in_array;
use function is_object;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class ByDefinitionNormalizer extends ObjectNormalizer
{
    public const MIGRATION_GROUP = 'migration';

    /** @var \Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader */
    private $definitionFileLoader;

    public function __construct(
        YamlFileLoader $definitionFileLoader,
        ClassMetadataFactoryInterface $classMetadataFactory,
        NameConverterInterface $nameConverter,
        array $defaultContext
    ) {
        $this->definitionFileLoader = $definitionFileLoader;

        parent::__construct(
            $classMetadataFactory,
            $nameConverter,
            $propertyAccessor = null,
            $propertyTypeExtractor = null,
            $classDiscriminatorResolver = null,
            $objectClassResolver = null,
            $defaultContext
        );
    }

    public function supportsNormalization($data, string $format = null)
    {
        if (!is_object($data)) {
            return false;
        }

        return in_array(
            get_class($data),
            $this->definitionFileLoader->getMappedClasses()
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return in_array(
            $type,
            $this->definitionFileLoader->getMappedClasses()
        );
    }
}

class_alias(ByDefinitionNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Generic\ByDefinitionNormalizer');
