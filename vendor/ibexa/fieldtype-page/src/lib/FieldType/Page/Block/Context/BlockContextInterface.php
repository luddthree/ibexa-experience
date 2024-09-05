<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Context;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;

/**
 * Provides context in which Block is rendered.
 *
 * Most used context is Content View in which Location and Content are provided.
 */
interface BlockContextInterface
{
    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page;
}

class_alias(BlockContextInterface::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Context\BlockContextInterface');
