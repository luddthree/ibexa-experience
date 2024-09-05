<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Webmozart\Assert\Assert;

final class ContentLocationIdResolver implements ContentResolverInterface
{
    public static function getHandledType(): string
    {
        return 'location_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, Content $content): Reference
    {
        $value = $content->contentInfo->mainLocationId;
        Assert::notNull($value);

        return Reference::create(
            $referenceDefinition->getName(),
            $value,
        );
    }
}

class_alias(ContentLocationIdResolver::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\Content\ContentLocationIdResolver');
