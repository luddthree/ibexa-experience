<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Webmozart\Assert\Assert;

abstract class AbstractReferenceGenerator
{
    private const RELATION_NAME_PATTERN = '%s__%s__%s';

    /**
     * @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[]
     */
    abstract public function generate(ValueObject $valueObject): array;

    /**
     * @return \Ibexa\Migration\Generator\Reference\ReferenceMetadata[]
     */
    abstract protected function getReferenceMetadata(): array;

    /**
     * @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[]
     */
    protected function generateReferences(string $objectTypeName, ?string $objectName): array
    {
        return array_map(function (ReferenceMetadata $referenceMetadata) use ($objectTypeName, $objectName) {
            return new ReferenceDefinition(
                $this->createRelationName($referenceMetadata->getNamePrefix(), $objectTypeName, $objectName),
                $referenceMetadata->getType()
            );
        }, $this->getReferenceMetadata());
    }

    private function createRelationName(string $referenceNamePrefix, string $contentTypeName, ?string $contentName): string
    {
        return sprintf(
            self::RELATION_NAME_PATTERN,
            $referenceNamePrefix,
            $contentTypeName,
            $contentName ? $this->underscore($contentName) : ''
        );
    }

    private function underscore(string $string): string
    {
        $string = preg_replace('/(?<=[a-z0-9])([A-Z])/', '_$1', $string);

        Assert::string($string);

        return strtolower($string);
    }
}

class_alias(AbstractReferenceGenerator::class, 'Ibexa\Platform\Migration\Generator\Reference\AbstractReferenceGenerator');
