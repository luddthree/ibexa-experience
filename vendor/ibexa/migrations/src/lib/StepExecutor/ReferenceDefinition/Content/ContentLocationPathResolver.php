<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\Content;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Webmozart\Assert\Assert;

final class ContentLocationPathResolver implements ContentResolverInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(
        LocationService $locationService
    ) {
        $this->locationService = $locationService;
    }

    public static function getHandledType(): string
    {
        return 'path';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, Content $content): Reference
    {
        $mainLocationId = $content->contentInfo->mainLocationId;
        Assert::notNull($mainLocationId);
        $pathString = $this->locationService->loadLocation($mainLocationId)->pathString;

        Assert::notNull($pathString);

        return Reference::create(
            $referenceDefinition->getName(),
            $pathString,
        );
    }
}

class_alias(ContentLocationPathResolver::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\Content\ContentLocationPathResolver');
