<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

use Ibexa\ContentForms\Data\Content\ContentUpdateData;

class TaggedContentUpdateData extends ContentUpdateData implements TaggableContentData
{
    use TaggableContentDataTrait;

    public static function fromContentUpdateData(ContentUpdateData $data): self
    {
        $properties = [];
        foreach ($data->getProperties() as $property) {
            $properties[$property] = $data->{$property};
        }

        return new self($properties);
    }
}
