<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Registry;

use Ibexa\Workflow\Exception\NotFoundException;
use Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface;

class MatcherValueMapperRegistry implements MatcherValueMapperRegistryInterface
{
    /** @var array */
    private $matcherMappers;

    /**
     * @param iterable $matcherMappers
     */
    public function __construct(iterable $matcherMappers = [])
    {
        /** @var \Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface $matcher */
        foreach ($matcherMappers as $matcher) {
            $this->set($matcher->getIdentifier(), $matcher);
        }
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function get(string $identifier): MatcherValueMapperInterface
    {
        if ($this->has($identifier)) {
            return $this->matcherMappers[$identifier];
        }

        throw new NotFoundException('MatcherValueMapper', $identifier);
    }

    /**
     * @param string $identifier
     * @param \Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface $matcherMapper
     */
    public function set(string $identifier, MatcherValueMapperInterface $matcherMapper): void
    {
        $this->matcherMappers[$identifier] = $matcherMapper;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool
    {
        return isset($this->matcherMappers[$identifier]);
    }
}

class_alias(MatcherValueMapperRegistry::class, 'EzSystems\EzPlatformWorkflow\Registry\MatcherValueMapperRegistry');
