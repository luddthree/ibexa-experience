<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\FieldType\ImageAsset;

use Ibexa\Core\FieldType\ImageAsset\Value as BaseValue;

class Value extends BaseValue
{
    /** @var string */
    public $source;

    public function __construct(
        $destinationContentId = null,
        ?string $alternativeText = null,
        ?string $source = null
    ) {
        parent::__construct($destinationContentId, $alternativeText);
        $this->source = $source;
    }
}

class_alias(Value::class, 'Ibexa\Platform\Connector\Dam\FieldType\ImageAsset\Value');
