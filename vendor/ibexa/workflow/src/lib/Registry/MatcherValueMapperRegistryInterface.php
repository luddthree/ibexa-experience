<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Registry;

use Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface;

interface MatcherValueMapperRegistryInterface
{
    /**
     * @param string $identifier
     *
     * @return \Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function get(string $identifier): MatcherValueMapperInterface;

    /**
     * @param string $identifier
     * @param \Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface $matcherMapper
     */
    public function set(string $identifier, MatcherValueMapperInterface $matcherMapper): void;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool;
}

class_alias(MatcherValueMapperRegistryInterface::class, 'EzSystems\EzPlatformWorkflow\Registry\MatcherValueMapperRegistryInterface');
