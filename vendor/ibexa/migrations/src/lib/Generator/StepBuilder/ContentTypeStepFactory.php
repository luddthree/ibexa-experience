<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Webmozart\Assert\Assert;

class ContentTypeStepFactory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     */
    protected function prepareLogMessage(ValueObject $contentType, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($contentType, ContentType::class);

        return sprintf('[Step] Preparing %s %s for [%s]', $type, $mode->getMode(), $contentType->identifier);
    }
}

class_alias(ContentTypeStepFactory::class, 'Ibexa\Platform\Migration\Generator\StepBuilder\ContentTypeStepFactory');
