<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ActivityLog\EventSubscriber\PageBuilder;

use Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder\ActivityLogEventListener;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalOr;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ActivityLog\EventSubscriber\PageBuilder\ActivityLogEventListener
 */
final class ActivityLogEventListenerTest extends TestCase
{
    /**
     * @var \Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private ActivityLogServiceInterface $activityLogService;

    private ActivityLogEventListener $listener;

    /**
     * @return iterable<string, array{array<mixed>, \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query}>
     */
    public static function provideForPreRenderEvent(): iterable
    {
        yield 'empty' => [
            [],
            new Query([], [new LoggedAtSortClause('DESC')]),
        ];

        yield 'limit' => [
            ['limit' => 42],
            new Query([], [new LoggedAtSortClause('DESC')], 0, 42),
        ];

        yield 'empty users array' => [
            [
                'user' => [],
            ],
            new Query([], [new LoggedAtSortClause('DESC')]),
        ];

        yield 'single user value' => [
            [
                'user' => 14,
            ],
            new Query(
                [new UserCriterion([14])],
                [new LoggedAtSortClause('DESC')],
            ),
        ];

        yield 'multiple users' => [
            [
                'user' => [14, 10],
            ],
            new Query(
                [new UserCriterion([14, 10])],
                [new LoggedAtSortClause('DESC')],
            ),
        ];

        /** @var class-string $classString */
        $classString = 'foo';

        yield 'object class (single)' => [
            [
                'object_class' => $classString,
            ],
            new Query(
                [new LogicalOr([
                    new ObjectCriterion($classString),
                ])],
                [new LoggedAtSortClause('DESC')],
            ),
        ];

        yield 'object class (array)' => [
            [
                'object_class' => [$classString, $classString],
            ],
            new Query(
                [new LogicalOr([
                    new ObjectCriterion($classString),
                    new ObjectCriterion($classString),
                ])],
                [new LoggedAtSortClause('DESC')],
            ),
        ];

        yield 'object class (empty array)' => [
            [
                'object_class' => [],
            ],
            new Query([], [new LoggedAtSortClause('DESC')]),
        ];
    }

    protected function setUp(): void
    {
        $this->activityLogService = $this->createMock(ActivityLogServiceInterface::class);
        $this->listener = new ActivityLogEventListener($this->activityLogService);
    }

    /**
     * @param array<mixed> $parameters
     *
     * @dataProvider provideForPreRenderEvent
     */
    public function testPreRenderEvent(array $parameters, Query $expectedQuery): void
    {
        $parameters = $this->initializeParameters($parameters);
        $renderRequest = new TwigRenderRequest('any template', $parameters);

        $context = $this->createMock(BlockContextInterface::class);

        $value = $this->createMock(BlockValue::class);

        $event = new PreRenderEvent($context, $value, $renderRequest);

        $this->activityLogService->expects(self::once())
            ->method('findGroups')
            ->with(
                self::equalTo($expectedQuery),
            )
            ->willReturn($this->createMock(ActivityGroupListInterface::class));

        $this->listener->preRenderEvent($event);
    }

    /**
     * @param array<mixed> $parameters
     *
     * @return array<mixed>
     */
    private function initializeParameters(array $parameters): array
    {
        if (isset($parameters['user'])) {
            $convertToUser = function (int $data): User {
                $user = $this->createMock(User::class);
                $user->method('getUserId')->willReturn($data);

                return $user;
            };

            if (is_array($parameters['user'])) {
                $parameters['user'] = array_map($convertToUser, $parameters['user']);
            } else {
                $parameters['user'] = $convertToUser($parameters['user']);
            }
        }

        if (isset($parameters['object_class'])) {
            $convertToObjectClass = function (string $data): ObjectClassInterface {
                $objectClass = $this->createMock(ObjectClassInterface::class);
                $objectClass->method('getObjectClass')->willReturn($data);

                return $objectClass;
            };

            if (is_array($parameters['object_class'])) {
                $parameters['object_class'] = array_map($convertToObjectClass, $parameters['object_class']);
            } else {
                $parameters['object_class'] = $convertToObjectClass($parameters['object_class']);
            }
        }

        return $parameters;
    }
}
