<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;

class LoggerContentTypeCreateStepBuilder implements StepBuilderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface */
    private $contentCreateStepBuilder;

    public function __construct(
        StepBuilderInterface $contentCreateStepBuilder
    ) {
        $this->contentCreateStepBuilder = $contentCreateStepBuilder;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     */
    public function build(ValueObject $contentType): StepInterface
    {
        $this->getLogger()->notice(sprintf('[Step] Preparing content type create for %s', $contentType->identifier));

        return $this->contentCreateStepBuilder->build($contentType);
    }
}

class_alias(LoggerContentTypeCreateStepBuilder::class, 'Ibexa\Platform\Migration\Generator\StepBuilder\LoggerContentTypeCreateStepBuilder');
