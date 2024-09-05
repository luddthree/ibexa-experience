<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\LandingPage\Converter;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;

/**
 * Decodes and encodes page object.
 *
 * @method string encode(\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $object)
 * @method \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page decode(string $json)
 * @method array toArray(\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $object, \JMS\Serializer\SerializationContext $serializationContext = null)
 * @method \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page fromArray(array $array)
 */
class PageConverter extends AbstractSerializerBasedConverter
{
    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return Page::class;
    }
}

class_alias(PageConverter::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Converter\PageConverter');
