<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

use Ibexa\ContentForms\Data\Content\ContentCreateData;

class TaggedContentCreateData extends ContentCreateData implements TaggableContentData
{
    use TaggableContentDataTrait;

    public static function fromContentCreateData(ContentCreateData $data): self
    {
        // public and protected properties
        $properties = [];
        foreach ($data->getProperties() as $property) {
            $properties[$property] = $data->{$property};
        }

        $taggedContentCreateData = new self($properties);
        $locationCreateStructs = $data->getLocationStructs();
        foreach ($locationCreateStructs as $locationCreateStruct) {
            $taggedContentCreateData->addLocationStruct($locationCreateStruct);
        }

        return $taggedContentCreateData;
    }
}
