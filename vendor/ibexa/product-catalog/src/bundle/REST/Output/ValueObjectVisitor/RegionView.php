<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Region as RestRegion;
use Ibexa\Bundle\ProductCatalog\REST\Value\RegionList as RestRegionList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class RegionView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'RegionView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const REGION_QUERY_IDENTIFIER = 'RegionQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\RegionView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restRegions = [];
        $viewIdentifier = $data->getIdentifier();
        $regionList = $data->getRegionList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::REGION_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::REGION_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $regionList->getTotalCount());

        foreach ($regionList->getRegions() as $Region) {
            $restRegions[] = new RestRegion($Region);
        }

        $visitor->visitValueObject(new RestRegionList($restRegions));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
