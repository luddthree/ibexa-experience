<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Security\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Persistence\Handler as SPIPersistenceHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Limitation\AbstractPersistenceLimitationType;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry as PersistenceTaxonomyEntry;
use Ibexa\Taxonomy\Security\Limitation\Value\TaxonomyLimitation;
use Ibexa\Taxonomy\Security\ValueObject\TaxonomyValue;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

final class TaxonomyLimitationType extends AbstractPersistenceLimitationType implements SPILimitationTypeInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        SPIPersistenceHandler $persistence,
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        parent::__construct($persistence);

        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof TaxonomyLimitation) {
            throw new InvalidArgumentType(
                '$limitationValue',
                'TaxonomyLimitation',
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
        $taxonomies = $this->taxonomyConfiguration->getTaxonomies();
        $validationErrors = [];

        foreach ($limitationValue->limitationValues as $key => $taxonomyName) {
            if (!in_array($taxonomyName, $taxonomies, true)) {
                $validationErrors[] = new ValidationError(
                    "limitationValues[%key%] => '%value%' does not exist in the backend",
                    null,
                    [
                        '%value%' => $taxonomyName,
                        '%key%' => $key,
                    ]
                );
            }
        }

        return $validationErrors;
    }

    public function buildValue(array $limitationValues): TaxonomyLimitation
    {
        return new TaxonomyLimitation(['limitationValues' => $limitationValues]);
    }

    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): bool {
        if (!$value instanceof TaxonomyLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: TaxonomyLimitation');
        }

        if (empty($value->limitationValues)) {
            return SPILimitationTypeInterface::ACCESS_DENIED;
        }

        $taxonomyName = null;
        if ($object instanceof TaxonomyEntry) {
            $taxonomyName = $object->taxonomy;
        } elseif ($object instanceof PersistenceTaxonomyEntry) {
            $taxonomyName = $object->getTaxonomy();
        } elseif ($object instanceof TaxonomyEntryCreateStruct) {
            $taxonomyName = $object->taxonomy;
        } elseif ($object instanceof TaxonomyValue) {
            $taxonomyName = $object->taxonomy;
        }

        return in_array($taxonomyName, $value->limitationValues, true)
            ? SPILimitationTypeInterface::ACCESS_GRANTED
            : SPILimitationTypeInterface::ACCESS_DENIED;
    }

    public function getCriterion(
        APILimitationValue $value,
        APIUserReference $currentUser
    ): CriterionInterface {
        throw new NotImplementedException(__METHOD__);
    }

    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }
}
