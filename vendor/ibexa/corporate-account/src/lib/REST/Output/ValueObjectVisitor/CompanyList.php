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
final class CompanyList extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\CorporateAccount\REST\Value\CompanyList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('CompanyList');
        $visitor->setHeader('Content-Type', $generator->getMediaType('CompanyList'));

        $generator->startList('companies');
        foreach ($data as $company) {
            $visitor->visitValueObject($company);
        }
        $generator->endList('companies');

        $generator->endObjectElement('CompanyList');
    }
}
