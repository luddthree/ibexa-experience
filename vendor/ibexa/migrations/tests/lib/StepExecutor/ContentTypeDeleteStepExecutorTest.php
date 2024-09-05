<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\StepExecutor\ContentTypeDeleteStepExecutor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\ContentTypeDeleteStepExecutor
 */
final class ContentTypeDeleteStepExecutorTest extends AbstractContentTypeExecutorTest
{
    /** @var \Ibexa\Migration\StepExecutor\ContentTypeDeleteStepExecutor */
    private $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executor = new ContentTypeDeleteStepExecutor(
            self::getContentTypeActionExecutor(),
            self::getContentTypeService(),
            self::getFieldTypeService(),
            self::getContentTypeFinderRegistry(),
        );

        $this->configureExecutor($this->executor, [
            ResolverInterface::class => self::getReferenceResolver('content_type'),
        ]);
    }

    /**
     * @dataProvider provideStepsForDefaultHandling
     */
    public function testHandleActions(ContentTypeDeleteStep $step): void
    {
        $contentType = $this->findContentType();
        self::assertNotNull($contentType);

        $this->executor->handle($step);

        self::assertNull($this->findContentType());
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\ContentTypeDeleteStep}>
     */
    public function provideStepsForDefaultHandling(): iterable
    {
        yield [
            new ContentTypeDeleteStep(new Matcher('content_type_identifier', 'article')),
        ];

        yield [
            new ContentTypeDeleteStep(new Matcher('location_remote_id', 'c15b600eb9198b1924063b5a68758232')),
        ];
    }

    private function findContentType(): ?ContentType
    {
        try {
            return self::getContentTypeService()->loadContentTypeByIdentifier('article');
        } catch (NotFoundException $e) {
            return null;
        }
    }
}

class_alias(ContentTypeDeleteStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ContentTypeDeleteStepExecutorTest');
