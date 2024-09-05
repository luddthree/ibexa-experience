<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper\CriterionMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class Catalog extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'Catalog';
    private const ATTRIBUTE_IDENTIFIER_IDENTIFIER = 'identifier';
    private const ATTRIBUTE_IDENTIFIER_NAME = 'name';
    private const ATTRIBUTE_IDENTIFIER_DESCRIPTION = 'description';
    private const ATTRIBUTE_IDENTIFIER_CREATED = 'created';
    private const ATTRIBUTE_IDENTIFIER_MODIFIED = 'modified';
    private const ATTRIBUTE_IDENTIFIER_STATUS = 'status';
    private const ATTRIBUTE_IDENTIFIER_USER = 'User';
    private const ATTRIBUTE_IDENTIFIER_CRITERIA = 'Criteria';

    /** @var iterable<\Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper\CriterionMapperInterface> */
    private iterable $mappers;

    /**
     * @param iterable<\Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper\CriterionMapperInterface> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Catalog $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $catalog = $data->catalog;
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_IDENTIFIER, $catalog->getIdentifier());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_NAME, $catalog->getName());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_DESCRIPTION, $catalog->getDescription());

        $userHref = $this->router->generate(
            'ibexa.rest.load_user',
            ['userId' => $catalog->getCreator()->getUserId()]
        );

        $generator->startObjectElement(self::ATTRIBUTE_IDENTIFIER_USER);
        $generator->attribute('href', $userHref);
        $generator->endObjectElement(self::ATTRIBUTE_IDENTIFIER_USER);

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_CREATED, $catalog->getCreated());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_MODIFIED, $catalog->getModified());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_STATUS, $catalog->getStatus());

        $generator->startList(self::ATTRIBUTE_IDENTIFIER_CRITERIA);
        $this->visitAppliedFilters($catalog, $visitor, $generator);
        $generator->endList(self::ATTRIBUTE_IDENTIFIER_CRITERIA);

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     */
    private function visitAppliedFilters(
        CatalogInterface $catalog,
        Visitor $visitor,
        Generator $generator
    ): void {
        $query = $catalog->getQuery();

        if (!$query instanceof LogicalAnd) {
            return;
        }

        foreach ($query->getCriteria() as $criterion) {
            $criterionName = $this->getCriterionName($criterion);
            if ($criterionName === null) {
                continue;
            }

            $mapper = $this->getSupportedMapper($criterion);
            if ($mapper === null) {
                throw new NotFoundException(
                    sprintf('%s criterion does not have its REST counterpart.', get_class($criterion))
                );
            }

            $restCriterion = $mapper->mapToRest($criterion);

            $generator->startObjectElement($criterionName);
            $visitor->visitValueObject($restCriterion);
            $generator->endObjectElement($criterionName);
        }
    }

    private function getCriterionName(CriterionInterface $criterion): ?string
    {
        $criterionClassName = strrchr(get_class($criterion), '\\');

        if ($criterionClassName === false) {
            return null;
        }

        return substr($criterionClassName, 1);
    }

    private function getSupportedMapper(CriterionInterface $criterion): ?CriterionMapperInterface
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($criterion)) {
                return $mapper;
            }
        }

        return null;
    }
}
