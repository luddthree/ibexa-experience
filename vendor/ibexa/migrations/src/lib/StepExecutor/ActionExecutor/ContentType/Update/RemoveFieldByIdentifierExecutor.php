<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\ContentType\Update;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveFieldByIdentifier;
use Webmozart\Assert\Assert;

final class RemoveFieldByIdentifierExecutor implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        ContentTypeService $contentTypeService
    ) {
        $this->contentTypeService = $contentTypeService;
    }

    public static function getExecutorKey(): string
    {
        return RemoveFieldByIdentifier::TYPE;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveFieldByIdentifier $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $valueObject
     */
    public function handle(Action $action, APIValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, RemoveFieldByIdentifier::class);
        Assert::isInstanceOf($valueObject, ContentType::class);

        $contentTypeDraft = $this->tryToCreateContentTypeDraft($valueObject);

        if (!$contentTypeDraft->hasFieldDefinition($action->getValue())) {
            $contentTypeFieldNames = implode(', ', $contentTypeDraft->getFieldDefinitions()->map(
                static function (FieldDefinition $item): string {
                    return $item->identifier;
                }
            ));

            throw new InvalidArgumentException('$action', sprintf(
                'Unable to find field with name %s, possible values: %s.',
                $action->getValue(),
                $contentTypeFieldNames
            ));
        }

        $fieldDefinition = $contentTypeDraft->fieldDefinitions->get($action->getValue());
        $this->contentTypeService->removeFieldDefinition($contentTypeDraft, $fieldDefinition);
        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function tryToCreateContentTypeDraft(ContentType $contentType): ContentTypeDraft
    {
        try {
            $contentTypeDraft = $this->contentTypeService->loadContentTypeDraft($contentType->id);
            $this->contentTypeService->deleteContentType($contentTypeDraft);
        } catch (NotFoundException $e) {
        } finally {
            return $this->contentTypeService->createContentTypeDraft($contentType);
        }
    }
}

class_alias(RemoveFieldByIdentifierExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\ContentType\Update\RemoveFieldByIdentifierExecutor');
