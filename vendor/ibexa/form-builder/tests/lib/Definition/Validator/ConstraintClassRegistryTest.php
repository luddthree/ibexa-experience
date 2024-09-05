<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\Definition\Validator;

use Ibexa\FormBuilder\Definition\Validator\ConstraintClassRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;

class ConstraintClassRegistryTest extends TestCase
{
    /** @var \Ibexa\FormBuilder\Definition\Validator\ConstraintClassRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $constraintClassRegistry;

    /** @var \Symfony\Component\Validator\Constraint */
    private $constraint;

    protected function setUp(): void
    {
        $this->constraintClassRegistry = new ConstraintClassRegistry();
        $this->constraint = new class() extends Constraint {
        };
    }

    public function testSetConstraintClass(): void
    {
        $this->assertFalse($this->constraintClassRegistry->hasConstraintClass('identifier'));

        $this->constraintClassRegistry->setConstraintClass('identifier', \get_class(new $this->constraint()));

        $this->assertTrue($this->constraintClassRegistry->hasConstraintClass('identifier'));
    }

    /**
     * @depends testSetConstraintClass
     */
    public function testHasConstraintClass(): void
    {
        $this->constraintClassRegistry->setConstraintClass('identifier', \get_class(new $this->constraint()));

        $this->assertTrue($this->constraintClassRegistry->hasConstraintClass('identifier'));
    }

    /**
     * @depends testSetConstraintClass
     */
    public function testGetConstraintClass(): void
    {
        $constraint = new $this->constraint();
        $this->constraintClassRegistry->setConstraintClass('identifier', \get_class($constraint));

        $constraintClass = $this->constraintClassRegistry->getConstraintClass('identifier');

        $this->assertEquals($constraint, new $constraintClass());
    }

    /**
     * @depends testSetConstraintClass
     */
    public function testGetConstraintClasses(): void
    {
        $constraintClasses = $this->constraintClassRegistry->getConstraintClasses();

        $this->assertEquals([], $constraintClasses);

        $constraint = new $this->constraint();
        $this->constraintClassRegistry->setConstraintClass('identifier', \get_class($constraint));

        $constraintClasses = $this->constraintClassRegistry->getConstraintClasses();
        $this->assertArrayHasKey('identifier', $constraintClasses);
    }

    public function testSetConstraintClasses(): void
    {
        $constraintClasses = $this->constraintClassRegistry->getConstraintClasses();

        $this->assertEquals([], $constraintClasses);

        $constraint1 = new $this->constraint();
        $constraint2 = new $this->constraint();
        $this->constraintClassRegistry->setConstraintClasses(
            [
                'identifier1' => \get_class($constraint1),
                'identifier2' => \get_class($constraint2),
            ]
        );

        $constraintClasses = $this->constraintClassRegistry->getConstraintClasses();

        $this->assertArrayHasKey('identifier1', $constraintClasses);
        $this->assertArrayHasKey('identifier2', $constraintClasses);
        $this->assertCount(2, $constraintClasses);
    }
}

class_alias(ConstraintClassRegistryTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\Definition\Validator\ConstraintClassRegistryTest');
