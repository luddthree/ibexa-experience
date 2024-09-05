<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Renderer;

use Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata;

interface MatcherBlockRendererInterface
{
    /**
     * Returns matcher value in human readable format.
     *
     * @param string $identifier
     * @param \Ibexa\Workflow\Value\WorkflowMatcherDefinitionMetadata $matcherDefinitionMetadata
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Ibexa\Workflow\Exception\NotFoundException
     */
    public function renderMatcherValue(string $identifier, WorkflowMatcherDefinitionMetadata $matcherDefinitionMetadata, array $parameters = []): string;
}

class_alias(MatcherBlockRendererInterface::class, 'EzSystems\EzPlatformWorkflow\Renderer\MatcherBlockRendererInterface');
