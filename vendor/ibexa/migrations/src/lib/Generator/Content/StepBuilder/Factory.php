<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Content\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use function sprintf;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     */
    protected function prepareLogMessage(ValueObject $content, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($content, Content::class);
        $contentTypeName = $content->getVersionInfo()->contentInfo->getContentType()->identifier;

        return sprintf('[Step] Preparing %s %s for [%s] %s', $type, $mode->getMode(), $contentTypeName, $content->getName());
    }
}

class_alias(Factory::class, 'Ibexa\Platform\Migration\Generator\Content\StepBuilder\Factory');
