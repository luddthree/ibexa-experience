<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteFactory\Validator\Constraints;

use Ibexa\Bundle\SiteFactory\Validator\Constraints\HostnameWithOptionalPort;
use Ibexa\Bundle\SiteFactory\Validator\Constraints\HostnameWithOptionalPortValidator;
use Ibexa\Bundle\SiteFactory\Validator\Constraints\Port;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class HostnameWithPortValidatorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ContextualValidatorInterface $innerValidator;

    private HostnameWithOptionalPortValidator $validator;

    protected function setUp(): void
    {
        $this->innerValidator = $this->createMock(ContextualValidatorInterface::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('inContext')->willReturn($this->innerValidator);

        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->executionContext->method('getValidator')->willReturn($validator);

        $this->validator = new HostnameWithOptionalPortValidator();
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

        $this->validator->validate($value, new HostnameWithOptionalPort());
    }

    /**
     * @return iterable<string, array{object}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'null' => [null];
    }

    /**
     * @dataProvider dataProviderForTestValidate
     */
    public function testValidate($value, array $expectedCalls): void
    {
        $actualCalls = [];

        // Validation should be delegated to dedicated constraints validators.
        $this->innerValidator
            ->method('validate')
            ->willReturnCallback(
                static function ($value, array $constraints) use (&$actualCalls) {
                    $actualCalls[] = [$value, $constraints];
                }
            );

        $this->validator->validate($value, new HostnameWithOptionalPort());

        $this->assertEquals($actualCalls, $expectedCalls);
    }

    public function dataProviderForTestValidate(): iterable
    {
        yield 'hostname only' => [
            'example.com',
            [
                ['example.com', [new Hostname()]],
            ],
        ];

        yield 'hostname with port' => [
            'example.com:80',
            [
                ['example.com', [new Hostname()]],
                ['80', [new Port()]],
            ],
        ];

        yield 'with missing port' => [
            'example.com:',
            [
                ['example.com', [new Hostname()]],
                ['', [new Port()]],
            ],
        ];

        yield 'with missing hostname' => [
            ':8000',
            [
                ['', [new Hostname()]],
                ['8000', [new Port()]],
            ],
        ];
    }
}
