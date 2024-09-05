<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Migration\StepExecutor\ContentTypeCreateStepExecutor;
use Ibexa\Migration\ValueObject\ContentType\CreateMetadata;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Tests\Bundle\Migration\MigrationTestAssertionsTrait;

/**
 * @covers \Ibexa\Migration\StepExecutor\ContentTypeCreateStepExecutor
 */
final class ContentTypeCreateStepExecutorTest extends AbstractContentTypeExecutorTest
{
    use MigrationTestAssertionsTrait;

    /** @var \Ibexa\Migration\StepExecutor\ContentTypeCreateStepExecutor */
    private $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executor = new ContentTypeCreateStepExecutor(
            self::getContentTypeActionExecutor(),
            self::getContentTypeService(),
            self::getFieldTypeService(),
            self::getUserService(),
            'admin',
        );

        $this->configureExecutor($this->executor);
    }

    public function testHandlesMissingDescriptionsForLanguages(): void
    {
        $metadata = CreateMetadata::createFromArray([
            'identifier' => 'test',
            'translations' => [
                'eng-GB' => [
                    'name' => 'test',
                ],
            ],
            'contentTypeGroups' => [1],
            'mainTranslation' => 'eng-GB',
        ]);

        $fieldDefinitions = FieldDefinitionCollection::create([
            FieldDefinition::createFromArray([
                'identifier' => 'foo',
                'type' => 'ezboolean',
                'translations' => [],
            ]),
        ]);

        $step = new ContentTypeCreateStep($metadata, $fieldDefinitions);

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        self::assertContentTypeExists('test');
    }
}

class_alias(ContentTypeCreateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ContentTypeCreateStepExecutorTest');
