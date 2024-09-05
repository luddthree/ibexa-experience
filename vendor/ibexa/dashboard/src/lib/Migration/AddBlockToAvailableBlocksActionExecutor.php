<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Migration;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;

final class AddBlockToAvailableBlocksActionExecutor implements ExecutorInterface
{
    private const AVAILABLE_BLOCKS_FIELD_SETTINGS = 'availableBlocks';

    private ContentTypeService $contentTypeService;

    public function __construct(
        ContentTypeService $contentTypeService
    ) {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param \Ibexa\Dashboard\Migration\AddBlockToAvailableBlocksAction $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handle(Action $action, ValueObject $contentType): void
    {
        $fieldDefinition = $contentType->getFieldDefinition(
            $action->getFieldDefinitionIdentifier()
        );

        if ($fieldDefinition === null) {
            return;
        }

        $fieldSettings = $fieldDefinition->getFieldSettings();
        $availableBlocks = $fieldSettings[self::AVAILABLE_BLOCKS_FIELD_SETTINGS] ?? [];

        $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);

        $fieldDefinitionCreateStruct = $this->contentTypeService->newFieldDefinitionUpdateStruct();
        $fieldDefinitionCreateStruct->fieldSettings = [
            self::AVAILABLE_BLOCKS_FIELD_SETTINGS => array_merge($availableBlocks, $action->getBlocks()),
        ];

        $this->contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $fieldDefinition,
            $fieldDefinitionCreateStruct
        );

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }
}
