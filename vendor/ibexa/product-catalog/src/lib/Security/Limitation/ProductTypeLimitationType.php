<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Security\Limitation;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Limitation\AbstractPersistenceLimitationType;
use Ibexa\ProductCatalog\Security\Limitation\Values\ProductTypeLimitation;
use RuntimeException;

final class ProductTypeLimitationType extends AbstractPersistenceLimitationType implements SPILimitationTypeInterface
{
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof ProductTypeLimitation) {
            throw new InvalidArgumentType(
                '$limitationValue',
                'ProductTypeLimitation',
                $limitationValue
            );
        }

        if (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType(
                '$limitationValue->limitationValues',
                'array',
                $limitationValue->limitationValues
            );
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            if (!is_string($value)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'string|int', $value);
            }
        }
    }

    public function validate(APILimitationValue $limitationValue): array
    {
        $validationErrors = [];
        foreach ($limitationValue->limitationValues as $key => $identifier) {
            try {
                $this->persistence->contentTypeHandler()->loadByIdentifier($identifier);
            } catch (APINotFoundException $e) {
                $validationErrors[] = new ValidationError(
                    "limitationValues[%key%] => '%value%' does not exist in the backend",
                    null,
                    [
                        'value' => $identifier,
                        'key' => $key,
                    ]
                );
            }
        }

        return $validationErrors;
    }

    public function buildValue(array $limitationValues): ProductTypeLimitation
    {
        return new ProductTypeLimitation(['limitationValues' => $limitationValues]);
    }

    public function evaluate(APILimitationValue $value, APIUserReference $currentUser, APIValueObject $object, array $targets = null)
    {
        $targets ??= [];
        foreach ($targets as $target) {
            if (!$target instanceof Target\Version) {
                continue;
            }

            $accessVote = $this->evaluateVersionTarget($target, $value);

            // continue evaluation of targets if there was no explicit grant/deny
            if ($accessVote === self::ACCESS_ABSTAIN) {
                continue;
            }

            return $accessVote;
        }
        if (!$value instanceof ProductTypeLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: ProductTypeLimitation');
        }

        if (empty($value->limitationValues)) {
            return false;
        }

        return in_array($this->getContentTypeIdentifier($object), $value->limitationValues, true);
    }

    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser): Criterion\ContentTypeIdentifier
    {
        /** @var array<string>|string $limitationValues */
        $limitationValues = $value->limitationValues;
        if (empty($limitationValues)) {
            // A Policy should not have empty limitationValues stored
            throw new RuntimeException('$value->limitationValues is empty');
        }

        return new Criterion\ContentTypeIdentifier($limitationValues);
    }

    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Evaluate permissions to create new Version.
     */
    private function evaluateVersionTarget(
        Target\Version $version,
        APILimitationValue $value
    ): ?bool {
        $accessVote = self::ACCESS_ABSTAIN;

        // ... unless there's a specific list of target translations
        if (!empty($version->allContentTypeIdsList)) {
            $accessVote = $this->evaluateMatchingAnyLimitation(
                $version->allContentTypeIdsList,
                $value->limitationValues
            );
        }

        return $accessVote;
    }

    /**
     * Allow access if any of the given ContentTypes matches any of the limitation values.
     *
     * @param int[] $contentTypeIdsList
     * @param string[] $limitationValues
     */
    private function evaluateMatchingAnyLimitation(
        array $contentTypeIdsList,
        array $limitationValues
    ): bool {
        $contentTypeIds = array_map('strval', $contentTypeIdsList);

        return empty(array_intersect($contentTypeIds, $this->getContentTypeIdsFromLimitation($limitationValues)))
            ? self::ACCESS_DENIED
            : self::ACCESS_GRANTED;
    }

    /**
     * @param array<string> $limitationValues
     *
     * @return array<int>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function getContentTypeIdsFromLimitation(array $limitationValues): array
    {
        $ids = [];
        foreach ($limitationValues as $contentTypeIdentifier) {
            /** @var int $contentTypeId */
            $contentTypeId = ($this->persistence->contentTypeHandler()->loadByIdentifier($contentTypeIdentifier))->id;
            $ids[] = $contentTypeId;
        }

        return $ids;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $object
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    private function getContentTypeIdentifier(APIValueObject $object): string
    {
        if ($object instanceof Content) {
            $object = $object->getVersionInfo();
        }

        if ($object instanceof VersionInfo) {
            $object = $object->getContentInfo();
        }

        if ($object instanceof ContentInfo) {
            return $object->getContentType()->identifier;
        }

        if ($object instanceof ContentCreateStruct) {
            return $object->contentType->identifier;
        }

        throw new InvalidArgumentException(
            '$object',
            'Must be of type: ContentCreateStruct, Content, VersionInfo or ContentInfo'
        );
    }
}
