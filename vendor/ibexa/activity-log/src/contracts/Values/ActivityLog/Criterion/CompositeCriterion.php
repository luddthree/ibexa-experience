<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

class CompositeCriterion implements CriterionInterface
{
    public const TYPE_AND = 'AND';

    public const TYPE_OR = 'OR';

    private const ALLOWED_TYPES = [self::TYPE_AND, self::TYPE_OR];

    /** @var \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface[] */
    public array $criteria;

    /** @phpstan-var self::TYPE* */
    private string $type;

    /**
     * @param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface[] $criteria
     *
     * @phpstan-param self::TYPE* $type
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function __construct(array $criteria, string $type)
    {
        $this->criteria = $criteria;
        $this->setType($type);
    }

    /**
     * @phpstan-param self::TYPE* $type
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function setType(string $type): void
    {
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('$type', sprintf(
                'Expected one of: "%s". Received "%s".',
                implode('", "', self::ALLOWED_TYPES),
                $type,
            ));
        }

        $this->type = $type;
    }

    /**
     * @phpstan-return self::TYPE*
     */
    final public function getType(): string
    {
        return $this->type;
    }
}
