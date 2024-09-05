<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Role;

use Webmozart\Assert\Assert;

final class PolicyList
{
    public const MODE_REPLACE = 'replace';
    public const MODE_APPEND = 'append';

    /** @var \Ibexa\Migration\ValueObject\Step\Role\Policy[] */
    private $policies;

    /** @var string */
    private $mode;

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\Policy[] $policies
     */
    public function __construct(array $policies, string $mode = self::MODE_REPLACE)
    {
        Assert::allIsInstanceOf($policies, Policy::class);
        $this->policies = $policies;
        $this->setMode($mode);
    }

    private function setMode(string $mode): void
    {
        if (!in_array($mode, [self::MODE_APPEND, self::MODE_REPLACE], true)) {
            throw new \InvalidArgumentException(sprintf(
                'Received "%s" as PolicyList mode of operation. Expected one of "%s".',
                $mode,
                implode('", "', [self::MODE_APPEND, self::MODE_REPLACE]),
            ));
        }

        $this->mode = $mode;
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Step\Role\Policy[]
     */
    public function getPolicies(): array
    {
        return $this->policies;
    }

    public function getMode(): string
    {
        return $this->mode;
    }
}
