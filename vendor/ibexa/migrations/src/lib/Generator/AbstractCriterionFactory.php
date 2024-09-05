<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\Generator\CriterionGenerator\GeneratorRegistryInterface;

abstract class AbstractCriterionFactory implements CriterionFactoryInterface
{
    private const WILDCARD = '*';

    /** @var \Ibexa\Migration\Generator\CriterionGenerator\GeneratorRegistryInterface */
    private $registry;

    public function __construct(GeneratorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    final public function build(?string $matchProperty, array $value = []): Criterion
    {
        $criteria = $this->getCommonCriteria();

        $criterion = $this->resolveMatchProperty($matchProperty, $value);
        if ($criterion !== null) {
            $criteria[] = $criterion;
        }

        return new Criterion\LogicalAnd($criteria);
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion>
     */
    protected function getCommonCriteria(): array
    {
        return [];
    }

    /**
     * @param array<mixed> $value
     */
    private function resolveMatchProperty(?string $matchProperty, array $value): ?Criterion
    {
        if (null === $matchProperty) {
            return null;
        }

        if (in_array(self::WILDCARD, $value, true)) {
            return null;
        }

        $builder = $this->registry->find($matchProperty);

        return $builder->generate($value);
    }
}

class_alias(AbstractCriterionFactory::class, 'Ibexa\Platform\Migration\Generator\AbstractCriterionFactory');
