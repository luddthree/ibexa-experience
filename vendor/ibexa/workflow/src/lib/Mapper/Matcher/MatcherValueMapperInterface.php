<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Mapper\Matcher;

interface MatcherValueMapperInterface
{
    /**
     * Map the matcher values, in order to pass them as context of matcher value rendering.
     *
     * @param array $matcherValues
     *
     * @return array
     */
    public function mapMatcherValue(array $matcherValues): array;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}

class_alias(MatcherValueMapperInterface::class, 'EzSystems\EzPlatformWorkflow\Mapper\Matcher\MatcherValueMapperInterface');
