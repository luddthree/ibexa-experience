<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\User;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentList;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\User\User;
use Ibexa\Migration\Generator\CriterionFactoryInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\Generator\User\UserMigrationGenerator;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\Migration\Generator\Content\UserMigrationGenerator
 */
final class UserMigrationGeneratorTest extends TestCase
{
    private const CHUNK_SIZE = 1;

    private UserMigrationGenerator $generator;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private UserService $userService;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private StepFactoryInterface $stepFactory;

    /** @var \Ibexa\Migration\ValueObject\Step\StepInterface|\PHPUnit\Framework\MockObject\MockObject */
    private StepInterface $step;

    private Mode $mode;

    protected function setUp(): void
    {
        $this->mode = new Mode('create');
        $this->step = $this->createMock(StepInterface::class);
        $this->contentService = $this->createMock(ContentService::class);
        $this->userService = $this->createMock(UserService::class);
        $this->stepFactory = $this->createMock(StepFactoryInterface::class);
        $criterionFactory = $this->createMock(CriterionFactoryInterface::class);

        $this->generator = new UserMigrationGenerator(
            $this->contentService,
            $this->userService,
            $this->stepFactory,
            $criterionFactory,
            self::CHUNK_SIZE
        );
    }

    public function testGenerateReturnsAllResultsInChunks(): void
    {
        $contentForCall0 = $this->buildUserContent(10);
        $contentForCall1 = $this->buildUserContent(14);

        $matcher = self::exactly(3);
        $this->contentService
            ->expects($matcher)
            ->method('find')
            ->willReturnCallback(static function (Filter $filter) use ($matcher, $contentForCall0, $contentForCall1): ContentList {
                self::assertSame(self::CHUNK_SIZE, $filter->getLimit(), sprintf(
                    'Invalid limit in call #%s findContent',
                    $matcher->getInvocationCount(),
                ));

                $message = 'Invalid offset in call #%s findContent';
                switch ($matcher->getInvocationCount()) {
                    case 1:
                        self::assertSame(0, $filter->getOffset(), sprintf(
                            $message,
                            $matcher->getInvocationCount(),
                        ));

                        return new ContentList(1, [$contentForCall0]);
                    case 2:
                        self::assertSame(1, $filter->getOffset(), sprintf(
                            $message,
                            $matcher->getInvocationCount(),
                        ));

                        return new ContentList(1, [$contentForCall1]);
                    case 3:
                        self::assertSame(2, $filter->getOffset(), sprintf(
                            $message,
                            $matcher->getInvocationCount(),
                        ));

                        return new ContentList(0, []);
                }

                return new ContentList(0, []);
            })
        ;

        $userForCall0 = new User([
            'content' => $contentForCall0,
        ]);
        $userForCall1 = new User([
            'content' => $contentForCall1,
        ]);

        $matcher = self::exactly(2);
        $this->userService
            ->expects($matcher)
            ->method('loadUser')
            ->willReturnCallback(static function (int $userId) use ($matcher, $userForCall0, $userForCall1): ?User {
                switch ($matcher->getInvocationCount()) {
                    case 1:
                        self::assertSame($userForCall0->id, $userId);

                        return $userForCall0;
                    case 2:
                        self::assertSame($userForCall1->id, $userId);

                        return $userForCall1;
                    default:
                        return null;
                }
            });

        $matcher = self::exactly(2);
        $this->stepFactory
            ->expects($matcher)
            ->method('create')
            ->with(self::callback(static function (User $user) use ($matcher, $userForCall0, $userForCall1): bool {
                switch ($matcher->getInvocationCount()) {
                    case 1:
                        self::assertSame($userForCall0, $user);
                        break;
                    case 2:
                        self::assertSame($userForCall1, $user);
                        break;
                }

                return true;
            }))
            ->willReturn($this->step);

        $results = $this->generator->generate(
            $this->mode,
            [
                'value' => ['*'],
                'match-property' => null,
            ]
        );

        self::assertInstanceOf(Traversable::class, $results);
        $resultsAsArray = iterator_to_array($results, false);
        self::assertCount(2, $resultsAsArray);
        self::assertContainsOnlyInstancesOf(StepInterface::class, $resultsAsArray);
    }

    private function buildUserContent(int $id): Content
    {
        return new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'id' => $id,
                ]),
            ]),
            'contentType' => new ContentType([
                'identifier' => 'user',
            ]),
        ]);
    }
}
