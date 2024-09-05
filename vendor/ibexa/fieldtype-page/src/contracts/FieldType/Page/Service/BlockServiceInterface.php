<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\FieldTypePage\FieldType\Page\Service;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Symfony\Component\HttpFoundation\Request;

interface BlockServiceInterface
{
    public const BLOCK_NAME_MAX_LENGTH = 255;

    /**
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     *
     * @throws \Ibexa\FieldTypePage\Exception\Registry\AttributeValidatorNotFoundException
     */
    public function validateBlock(BlockValue $block, BlockDefinition $blockDefinition): array;

    /**
     * @throws \Exception
     */
    public function render(BlockContextInterface $blockContext, BlockValue $blockValue): string;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function createBlockContextFromRequest(Request $request): BlockContextInterface;
}

class_alias(BlockServiceInterface::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Service\BlockServiceInterface');
