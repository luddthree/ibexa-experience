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
use Ibexa\CorporateAccount\REST\Value\Link as RestLink;

/**
 * @internal
 */
final class Link extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\CorporateAccount\REST\Value\Link $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('link', $data->getMediaType());

        $generator->attribute('rel', $data->getRel());
        $this->generateResourceHref($generator, $data);

        $generator->endObjectElement('link');
    }

    private function generateResourceHref(Generator $generator, RestLink $data): void
    {
        $generator->attribute(
            'href',
            $this->router->generate(
                $data->getRouteName(),
                $data->getRouteParameters()
            )
        );
    }
}
