<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Migration\Action\PutBlockOntoPage;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;

/**
 * @internal
 */
final class PutBlockOntoPageActionExecutor implements ExecutorInterface
{
    private ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param \Ibexa\FieldTypePage\Migration\Action\PutBlockOntoPage\PutBlockOntoPageAction $action
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $contentItem
     *
     * @throws \Exception
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function handle(Action $action, ValueObject $contentItem): void
    {
        $fieldDefinitionIdentifier = $action->getFieldDefinitionIdentifier();
        $fieldValue = $this->getFieldValue($contentItem, $fieldDefinitionIdentifier);

        foreach ($action->getZoneBlocksList() as $zoneName => $blocks) {
            $zone = $fieldValue->getPage()->getZoneByName($zoneName);
            foreach ($blocks as $blockValue) {
                $zone->addBlock($blockValue);
            }
        }

        $contentDraft = $this->contentService->createContentDraft($contentItem->getVersionInfo()->getContentInfo());
        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        $contentUpdateStruct->setField($fieldDefinitionIdentifier, $fieldValue);
        $content = $this->contentService->updateContent($contentDraft->getVersionInfo(), $contentUpdateStruct);
        $this->contentService->publishVersion($content->getVersionInfo());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getFieldValue(Content $contentItem, string $fieldDefinitionIdentifier): Value
    {
        $field = $contentItem->getField($fieldDefinitionIdentifier);
        if ($field === null) {
            throw new InvalidArgumentException(
                'fieldDefinitionIdentifier',
                sprintf(
                    'Content ID=%d, Name="%s" doesn\'t have "%s" field definition',
                    $contentItem->getVersionInfo()->getContentInfo()->getId(),
                    $contentItem->getName(),
                    $fieldDefinitionIdentifier
                )
            );
        }

        $fieldValue = $field->getValue();
        if (!$fieldValue instanceof Value) {
            throw new InvalidArgumentException(
                "field=$fieldDefinitionIdentifier",
                sprintf(
                    'Expected field value of Content ID=%d, Name="%s" to be of "%s" type, got: "%s" instead',
                    $contentItem->getVersionInfo()->getContentInfo()->getId(),
                    $contentItem->getName(),
                    Value::class,
                    get_class($fieldValue)
                )
            );
        }

        return $fieldValue;
    }
}
