<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\REST\Server\Output\Generator;

use Ibexa\Rest\Output\Generator\Json as BaseJson;

/**
 * Json generator.
 */
class Json extends BaseJson
{
    protected $baseGenerator;

    /**
     * Json constructor.
     */
    public function __construct(BaseJson $baseGenerator)
    {
        $this->baseGenerator = $baseGenerator;
    }

    use MediaTypeTrait;
}

class_alias(Json::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Output\Generator\Json');
