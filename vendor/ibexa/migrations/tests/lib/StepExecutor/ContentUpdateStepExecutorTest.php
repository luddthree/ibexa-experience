<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\StepExecutor\ContentUpdateStepExecutor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\ContentUpdateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\ContentUpdateStepExecutor
 */
final class ContentUpdateStepExecutorTest extends AbstractContentStepExecutorTest
{
    private const KNOWN_CONTENT_ID = 57;
    private const KNOWN_CONTENT_REMOTE_ID = '8a9c9c761004866fb458d89910f52bee';
    private const NEW_CONTENT_NAME = '__new_name__';
    private const NEW_REMOTE_ID = '__new_remote_id__';

    /**
     * @dataProvider provideSteps
     */
    public function testHandle(ContentUpdateStep $step): void
    {
        self::assertSame(1, self::findContentByIdentifier(self::KNOWN_CONTENT_TYPE_IDENTIFIER)->getTotalCount());
        $content = self::getContentService()->loadContentByRemoteId(self::KNOWN_CONTENT_REMOTE_ID);
        $contentInfo = $content->contentInfo;

        self::assertInstanceOf(Content::class, $content);
        self::assertNotSame(self::NEW_CONTENT_NAME, $content->getName());

        self::assertSame('Home', $contentInfo->name);
        self::assertSame(14, $contentInfo->ownerId);
        self::assertTrue($contentInfo->alwaysAvailable);
        self::assertEquals(new DateTime('2007-11-28T16:51:36+00:00'), $contentInfo->modificationDate);
        self::assertEquals(new DateTime('2007-11-19T13:54:46+00:00'), $contentInfo->publishedDate);
        self::assertSame(2, $contentInfo->mainLocationId);
        self::assertSame('eng-GB', $contentInfo->mainLanguageCode);

        $executor = new ContentUpdateStepExecutor(
            self::getContentService(),
            self::getFieldTypeService(),
            $actionsExecutor = $this->createMock(ExecutorInterface::class),
        );
        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('content'),
        ]);

        $actionsExecutor
            ->expects(self::once())
            ->method('handle');

        $executor->handle($step);

        self::assertSame(1, self::findContentByIdentifier(self::KNOWN_CONTENT_TYPE_IDENTIFIER)->getTotalCount());
        $content = self::getContentService()->loadContentByRemoteId(self::NEW_REMOTE_ID);
        $contentInfo = $content->contentInfo;

        self::assertInstanceOf(Content::class, $content);
        self::assertSame('Home', $content->getName('eng-GB'));
        self::assertSame(self::NEW_CONTENT_NAME, $content->getName());
        self::assertSame(self::KNOWN_CONTENT_TYPE_IDENTIFIER, $content->getContentType()->identifier);
        self::assertSame(self::NEW_REMOTE_ID, $contentInfo->remoteId);
        self::assertSame(self::NEW_CONTENT_NAME, $contentInfo->name);
        self::assertSame(10, $contentInfo->ownerId);
        self::assertFalse($contentInfo->alwaysAvailable);
        self::assertEquals(new DateTime('2020-12-28T13:21:00+00:00'), $contentInfo->publishedDate);
        self::assertSame('eng-US', $contentInfo->mainLanguageCode);
    }

    private function createStep(FilteringCriterion $criterion): ContentUpdateStep
    {
        return new ContentUpdateStep(
            UpdateMetadata::createFromArray([
                'initialLanguageCode' => 'eng-US',
                'creatorId' => 14,
                'remoteId' => self::NEW_REMOTE_ID,
                'alwaysAvailable' => false,
                'mainLanguageCode' => 'eng-US',
                'modificationDate' => '2020-12-28T13:20:00+00:00',
                'publishedDate' => '2020-12-28T13:21:00+00:00',
                'name' => '__NEW_NAME__',
                'ownerId' => 10,
            ]),
            $criterion,
            [
                Field::createFromArray([
                    'fieldDefIdentifier' => 'name',
                    'languageCode' => 'eng-US',
                    'value' => self::NEW_CONTENT_NAME,
                ]),
            ],
            [
                $this->createMock(Action::class),
            ]
        );
    }

    /**
     * @return iterable<array{ContentUpdateStep}>
     */
    public function provideSteps(): iterable
    {
        yield [
            $this->createStep(new Criterion\RemoteId(self::KNOWN_CONTENT_REMOTE_ID)),
        ];

        yield [
            $this->createStep(new Criterion\ContentId(self::KNOWN_CONTENT_ID)),
        ];
    }
}

class_alias(ContentUpdateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ContentUpdateStepExecutorTest');
