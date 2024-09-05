<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Taxonomy\REST\Values\RestTaxonomyEntry as ValueRestEntry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RestTaxonomyEntry extends ValueObjectVisitor
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        if (!$data instanceof ValueRestEntry || null === $data->getTaxonomyEntry()) {
            return;
        }

        $entry = $data->getTaxonomyEntry();

        $generator->startObjectElement('TaxonomyEntry');

        $generator->valueElement('id', $entry->id);
        $generator->valueElement('identifier', $entry->identifier);
        $generator->valueElement('name', $entry->name);
        $generator->valueElement('contentId', $entry->content->id);
        $generator->valueElement('level', $entry->level);

        $generator->startObjectElement('content', 'Content');
        $generator->attribute('href', $this->getContentRestUrl($entry->content));
        $generator->endObjectElement('content');

        $generator->valueElement('taxonomy', $entry->taxonomy);

        $generator->endObjectElement('TaxonomyEntry');
    }

    private function getContentRestUrl(Content $content): string
    {
        return $this->urlGenerator->generate(
            'ibexa.rest.load_content',
            [
                'contentId' => $content->contentInfo->id,
            ]
        );
    }
}
