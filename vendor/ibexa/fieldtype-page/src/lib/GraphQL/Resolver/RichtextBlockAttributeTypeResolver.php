<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Resolver;

use DOMDocument;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter as RichTextConverterInterface;

class RichtextBlockAttributeTypeResolver
{
    /** @var \Ibexa\Contracts\FieldTypeRichText\RichText\Converter */
    private $richTextConverter;

    /** @var \Ibexa\Contracts\FieldTypeRichText\RichText\Converter */
    private $richTextEditConverter;

    /**
     * @param \Ibexa\Contracts\FieldTypeRichText\RichText\Converter $richTextConverter
     * @param \Ibexa\Contracts\FieldTypeRichText\RichText\Converter $richTextEditConverter
     */
    public function __construct(
        RichTextConverterInterface $richTextConverter,
        RichTextConverterInterface $richTextEditConverter
    ) {
        $this->richTextConverter = $richTextConverter;
        $this->richTextEditConverter = $richTextEditConverter;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function resolveRichTextStringXmlToHtml5Edit(string $value): string
    {
        $document = $this->xmlDocumentFromString($value);

        return $this->richTextEditConverter->convert($document)->saveHTML();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function resolveRichTextStringXmlToHtml5(string $value): string
    {
        $document = $this->xmlDocumentFromString($value);

        return $this->richTextConverter->convert($document)->saveHTML();
    }

    /**
     * @param string $value
     *
     * @return \DOMDocument
     */
    private function xmlDocumentFromString(string $value): DOMDocument
    {
        $document = new DOMDocument();
        $document->loadXML($value);

        return $document;
    }
}

class_alias(RichtextBlockAttributeTypeResolver::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Resolver\RichtextBlockAttributeTypeResolver');
