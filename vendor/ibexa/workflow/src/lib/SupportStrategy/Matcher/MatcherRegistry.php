<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\SupportStrategy\Matcher;

use Ibexa\Contracts\Workflow\SupportStrategy\Matcher\MatcherInterface;
use Ibexa\Workflow\Exception\NotFoundException;

class MatcherRegistry
{
    /** @var array */
    private $matchers = [];

    /**
     * @param iterable $matchers
     */
    public function __construct(iterable $matchers = [])
    {
        /** @var \Ibexa\Contracts\Workflow\SupportStrategy\Matcher\MatcherInterface $matcher */
        foreach ($matchers as $matcher) {
            $this->set($matcher->getIdentifier(), $matcher);
        }
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\Workflow\SupportStrategy\Matcher\MatcherInterface
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function get(string $identifier): MatcherInterface
    {
        if ($this->has($identifier)) {
            return $this->matchers[$identifier];
        }

        throw new NotFoundException('Matcher', $identifier);
    }

    /**
     * @param string $identifier
     * @param \Ibexa\Contracts\Workflow\SupportStrategy\Matcher\MatcherInterface $matcher
     */
    public function set(string $identifier, MatcherInterface $matcher): void
    {
        $this->matchers[$identifier] = $matcher;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool
    {
        return isset($this->matchers[$identifier]);
    }
}

class_alias(MatcherRegistry::class, 'EzSystems\EzPlatformWorkflow\SupportStrategy\Matcher\MatcherRegistry');
