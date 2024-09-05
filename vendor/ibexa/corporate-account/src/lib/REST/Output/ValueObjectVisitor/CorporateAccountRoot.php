<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

/**
 * @internal
 */
final class CorporateAccountRoot extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\CorporateAccount\REST\Value\CorporateAccountRoot $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('CorporateAccountRoot');
        $visitor->setHeader('Content-Type', $generator->getMediaType('CorporateAccountRoot'));

        $generator->startList('links');
        foreach ($data as $link) {
            $visitor->visitValueObject($link);
        }
        $generator->endList('links');

        $generator->endObjectElement('CorporateAccountRoot');
    }
}
