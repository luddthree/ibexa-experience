<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\LandingPage;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    /**
     * Landing page object.
     *
     * @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    private $page;

    /**
     * Value constructor.
     *
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page|null $page
     */
    public function __construct(Page $page = null)
    {
        $this->page = $page;
    }

    /**
     * Returns page object.
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}

class_alias(Value::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Value');
