<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\Role\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation as ApiLimitation;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Core\Repository\Values\User\Policy;
use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Generator\Reference\RoleGenerator as ReferenceRoleGenerator;
use Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager;
use Ibexa\Migration\Generator\Role\StepBuilder\RoleUpdateStepBuilder;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Ibexa\Migration\ValueObject\Step\Role\Policy as StepPolicy;
use Ibexa\Migration\ValueObject\Step\RoleUpdateStep;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\Generator\Role\StepBuilder\RoleUpdateStepBuilder
 */
final class RoleUpdateStepBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $limitation = $this->createMock(ApiLimitation::class);
        $limitation->method('getIdentifier')
            ->willReturn('__identifier__');
        $limitation->limitationValues = ['__limit__'];

        $role = new Role([
            'identifier' => '__identifier__',
            'policies' => [
                new Policy([
                    'module' => '__module__',
                    'function' => '__function__',
                    'limitations' => [
                        $limitation,
                    ],
                ]),
            ],
        ]);

        $builder = new RoleUpdateStepBuilder(
            new ReferenceRoleGenerator(),
            new LimitationConverterManager(
                $this->createMock(LimitationService::class),
                [],
            ),
        );

        $step = $builder->build($role);
        $reference = new ReferenceDefinition('ref__role____role_id', 'role_id');

        /** @var \Ibexa\Migration\ValueObject\Step\RoleUpdateStep $step */
        self::assertInstanceOf(RoleUpdateStep::class, $step);
        self::assertSame('__identifier__', $step->metadata->identifier);
        $policyList = $step->getPolicyList();
        self::assertNotNull($policyList);
        self::assertSame('replace', $policyList->getMode());
        self::assertContainsOnlyInstancesOf(StepPolicy::class, $policyList->getPolicies());
        self::assertEquals([$reference], $step->references);
        $policy = $policyList->getPolicies()[0];
        self::assertSame('__module__', $policy->module);
        self::assertSame('__function__', $policy->function);
        self::assertEquals([
            new Limitation('__identifier__', ['__limit__']),
        ], $policy->limitations);
    }
}

class_alias(RoleUpdateStepBuilderTest::class, 'Ibexa\Platform\Tests\Migration\Generator\Role\StepBuilder\RoleUpdateStepBuilderTest');
