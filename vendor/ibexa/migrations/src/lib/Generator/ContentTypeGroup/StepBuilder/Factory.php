<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ContentTypeGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use function sprintf;

class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    protected function prepareLogMessage(ValueObject $contentTypeGroup, Mode $mode, string $type): ?string
    {
        $contentTypeName = $contentTypeGroup->identifier;

        return sprintf('[Step] Preparing %s %s for %s', $type, $mode->getMode(), $contentTypeName);
    }
}

class_alias(Factory::class, 'Ibexa\Platform\Migration\Generator\ContentTypeGroup\StepBuilder\Factory');
