<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\REST\Server\Output\Generator;

use Ibexa\Rest\Output\Generator\Xml as BaseXml;

/**
 * Xml generator.
 */
class Xml extends BaseXml
{
    protected $baseGenerator;

    /**
     * Xml constructor.
     */
    public function __construct(BaseXml $baseGenerator)
    {
        $this->baseGenerator = $baseGenerator;
    }

    use MediaTypeTrait;
}

class_alias(Xml::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Output\Generator\Xml');
