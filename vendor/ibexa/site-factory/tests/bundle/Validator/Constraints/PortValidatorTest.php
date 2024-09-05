<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteFactory\Validator\Constraints;

use Ibexa\Bundle\SiteFactory\Validator\Constraints\Port;
use Ibexa\Bundle\SiteFactory\Validator\Constraints\PortValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PortValidatorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private PortValidator $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new PortValidator();
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     *
     * @param mixed $value
     */
    public function testSkipValidation($value): void
    {
        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new Port());
    }

    /**
     * @return iterable<string, array{object}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'null' => [null];
    }

    /**
     * @dataProvider dataProviderForTestInvalidPort
     */
    public function testInvalidPort(int $value): void
    {
        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with(Port::MESSAGE)
            ->willReturn($this->getConstraintViolationBuilderMock());

        $this->validator->validate($value, new Port());
    }

    /**
     * @return iterable<string, array{int}>
     */
    public function dataProviderForTestInvalidPort(): iterable
    {
        yield 'negative' => [-1];
        yield 'too big' => [65536];
    }

    /**
     * @dataProvider dataProviderForTestValidPort
     */
    public function testValidPort(int $value): void
    {
        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new Port());
    }

    public function dataProviderForTestValidPort(): iterable
    {
        yield 'min value' => [0];
        yield 'max value' => [65535];
        yield 'in range' => [23456];
    }

    private function getConstraintViolationBuilderMock(): ConstraintViolationBuilderInterface
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('addViolation');

        return $builder;
    }
}
