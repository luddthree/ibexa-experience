<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypeRichText\RichText\InputHandlerInterface;
use Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper;
use Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class RichTextBlockAttributeRelationExtractor implements BlockAttributeRelationExtractorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\FieldTypeRichText\RichText\InputHandlerInterface */
    private $inputHandler;

    /** @var \Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory */
    private $domDocumentFactory;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper */
    private $contentLocationMapper;

    public function __construct(
        InputHandlerInterface $inputHandler,
        DOMDocumentFactory $domDocumentFactory,
        LocationService $locationService,
        ContentLocationMapper $contentLocationMapper
    ) {
        $this->inputHandler = $inputHandler;
        $this->domDocumentFactory = $domDocumentFactory;
        $this->locationService = $locationService;
        $this->logger = new NullLogger();
        $this->contentLocationMapper = $contentLocationMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): bool {
        return 'richtext' === $attributeDefinition->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function extract(
        Page $page,
        BlockValue $blockValue,
        BlockAttributeDefinition $attributeDefinition,
        Attribute $attribute
    ): array {
        $contentIds = [];

        $xml = $attribute->getValue();
        if (!empty($xml)) {
            $relationsByType = $this->inputHandler->getRelations(
                $this->domDocumentFactory->loadXMLString($xml)
            );

            foreach ($relationsByType as $type => $relations) {
                $contentIds = array_merge($contentIds, $relations['contentIds']);

                foreach ($relations['locationIds'] as $locationId) {
                    $locationId = (int) $locationId;
                    if ($this->contentLocationMapper->hasMapping($locationId)) {
                        $contentIds[] = $this->contentLocationMapper->getMapping($locationId);
                    } else {
                        try {
                            $contentIds[] = $this->locationService->loadLocation($locationId)->contentId;
                        } catch (NotFoundException | UnauthorizedException $e) {
                            $this->logger->warning("Unable to extract relation: {$e->getMessage()}", [
                                'exception' => $e,
                            ]);
                        }
                    }
                }
            }
        }

        return $contentIds;
    }
}

class_alias(RichTextBlockAttributeRelationExtractor::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Relation\Extractor\RichTextBlockAttributeRelationExtractor');
