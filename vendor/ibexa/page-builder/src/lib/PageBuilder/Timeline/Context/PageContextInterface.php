<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Context;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;

interface PageContextInterface extends ContextInterface
{
    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page;

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function setPage(Page $page): void;
}

class_alias(PageContextInterface::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Context\PageContextInterface');
