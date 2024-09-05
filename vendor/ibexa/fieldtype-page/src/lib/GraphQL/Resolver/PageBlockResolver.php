<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Resolver;

use ArrayObject;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\GraphQlQueryBlockContext;
use Ibexa\FieldTypePage\FieldType\Page\Service\BlockService;
use Overblog\GraphQLBundle\Definition\Argument;

class PageBlockResolver
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService */
    private $blockService;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Overblog\GraphQLBundle\Definition\Argument $args
     * @param \ArrayObject $context
     *
     * @return string
     */
    public function resolvePageBlockHtml(BlockValue $blockValue, Argument $args, ArrayObject $context): string
    {
        $blockContext = new GraphQlQueryBlockContext($args, $context);

        return $this->blockService->render($blockContext, $blockValue);
    }
}

class_alias(PageBlockResolver::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Resolver\PageBlockResolver');
