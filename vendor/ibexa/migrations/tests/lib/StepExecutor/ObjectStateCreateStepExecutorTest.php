<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Migration\StepExecutor\ObjectStateCreateStepExecutor;
use Ibexa\Migration\ValueObject\ObjectState\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\ObjectStateCreateStepExecutor
 */
final class ObjectStateCreateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    /**
     * @dataProvider provideSteps
     */
    public function testHandle(ObjectStateCreateStep $step): void
    {
        $found = true;
        try {
            $this->getObjectState($step->metadata->identifier);
        } catch (NotFoundException $e) {
            $found = false;
        }
        self::assertFalse($found);

        $executor = new ObjectStateCreateStepExecutor(
            self::getTransactionHandler(),
            self::getObjectStateService()
        );

        $executor->handle($step);

        $section = $this->getObjectState($step->metadata->identifier);

        self::assertSame($step->metadata->identifier, $section->identifier);
        self::assertSame($step->metadata->translations['eng-GB']['name'], $section->getName('eng-GB'));
        self::assertSame('ez_lock', $section->getObjectStateGroup()->identifier);
    }

    private function getObjectState(string $identifier): ObjectState
    {
        $objectStateService = self::getObjectStateService();
        $objectStateGroup = $objectStateService->loadObjectStateGroupByIdentifier('ez_lock');

        return $objectStateService->loadObjectStateByIdentifier($objectStateGroup, $identifier);
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep}>
     */
    public function provideSteps(): iterable
    {
        yield [
            new ObjectStateCreateStep(
                CreateMetadata::createFromArray([
                    'identifier' => 'new_state',
                    'mainTranslation' => 'eng-GB',
                    'objectStateGroup' => 2,
                    'priority' => false,
                    'translations' => [
                        'eng-GB' => [
                            'name' => 'New State',
                        ],
                    ],
                ])
            ),
            new ObjectStateCreateStep(
                CreateMetadata::createFromArray([
                    'identifier' => 'newer_state',
                    'mainTranslation' => 'eng-GB',
                    'objectStateGroup' => 'ez_lock',
                    'priority' => false,
                    'translations' => [
                        'eng-GB' => [
                            'name' => 'Newer State',
                        ],
                    ],
                ])
            ),
        ];
    }
}

class_alias(ObjectStateCreateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ObjectStateCreateStepExecutorTest');
