<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\Definition\Validator;

use Ibexa\FormBuilder\Definition\Validator\ConstraintClassRegistry;
use Ibexa\FormBuilder\Definition\Validator\ConstraintFactory;
use Ibexa\FormBuilder\Exception\ValidatorNotFoundException;
use PHPUnit\Framework\TestCase;

class ConstraintFactoryTest extends TestCase
{
    /** @var \Ibexa\FormBuilder\Definition\Validator\ConstraintClassRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $constraintClassRegistry;

    protected function setUp(): void
    {
        $this->constraintClassRegistry = new ConstraintClassRegistry();
    }

    public function testGetConstraint(): void
    {
        $this->constraintClassRegistry->setConstraintClass('identifier', ConstraintClassStub::class);

        $factory = new ConstraintFactory($this->constraintClassRegistry);

        $this->assertEquals(new ConstraintClassStub(), $factory->getConstraint('identifier', [
            'message' => 'some_message',
        ]));
    }

    public function testGetConstraintNonExistingConstraintClass(): void
    {
        $this->expectException(ValidatorNotFoundException::class);

        $factory = new ConstraintFactory($this->constraintClassRegistry);

        $factory->getConstraint('identifier', []);
    }

    public function testGetConstraintWithOptions(): void
    {
        $options = [
            'opt1' => 1,
            'opt2' => 2,
        ];
        $this->constraintClassRegistry->setConstraintClass('identifier', ConstraintClassStub::class);

        $factory = new ConstraintFactory($this->constraintClassRegistry);

        $this->assertEquals(new ConstraintClassStub($options), $factory->getConstraint(
            'identifier',
            [
                'message' => 'some_message',
                'options' => $options,
            ]
        ));
    }
}

class ConstraintClassStub
{
    public $message = 'some_message';

    public $options = [];

    public function __construct(array $options = [])
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }
}

class_alias(ConstraintFactoryTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\Definition\Validator\ConstraintFactoryTest');
