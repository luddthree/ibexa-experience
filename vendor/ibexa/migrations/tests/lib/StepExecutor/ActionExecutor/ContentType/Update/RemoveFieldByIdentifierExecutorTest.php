<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor\ActionExecutor\ContentType\Update;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\StepExecutor\ActionExecutor\ContentType\Update\RemoveFieldByIdentifierExecutor;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveFieldByIdentifier;
use PHPUnit\Framework\TestCase;

final class RemoveFieldByIdentifierExecutorTest extends TestCase
{
    public function testHandle(): void
    {
        $contentTypeService = $this->createContentTypeServiceMockWithEmptyFieldDefinitions();

        $executor = new RemoveFieldByIdentifierExecutor($contentTypeService);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument '\$action' is invalid: Unable to find field with name foo, possible values: .");

        $executor->handle(new RemoveFieldByIdentifier('foo'), $this->createMock(ContentType::class));
    }

    private function createContentTypeServiceMockWithEmptyFieldDefinitions(): ContentTypeService
    {
        $contentTypeDraft = $this->createMock(ContentTypeDraft::class);
        $contentTypeDraft
            ->method('hasFieldDefinition')
            ->willReturn(false);
        $contentTypeDraft
            ->method('getFieldDefinitions')
            ->willReturn(new FieldDefinitionCollection([]));

        $contentTypeService = $this->createMock(ContentTypeService::class);
        $contentTypeService
            ->method('loadContentTypeDraft')
            ->willReturn($this->createMock(ContentTypeDraft::class));
        $contentTypeService
            ->method('createContentTypeDraft')
            ->willReturn($contentTypeDraft);

        return $contentTypeService;
    }
}

class_alias(RemoveFieldByIdentifierExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ActionExecutor\ContentType\Update\RemoveFieldByIdentifierExecutorTest');
