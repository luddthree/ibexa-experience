<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer;

use Ibexa\Bundle\Migration\Serializer\Normalizer\ExpressionNormalizer;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer\DummyStep;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\ExpressionNormalizer
 */
final class ExpressionNormalizerTest extends IbexaKernelTestCase
{
    private ExpressionNormalizer $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $innerDenormalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\Serializer\Normalizer\NormalizerInterface */
    private $innerNormalizer;

    protected function setUp(): void
    {
        $this->normalizer = self::getServiceByClassName(ExpressionNormalizer::class);

        $collector = self::getServiceByClassName(CollectorInterface::class);
        $collector->collect(Reference::create('foo', '"Woah! A reference!"'));

        $this->innerDenormalizer = $this->createMock(DenormalizerInterface::class);
        $this->innerDenormalizer
            ->method('denormalize')
            ->willReturnArgument(0);
        $this->normalizer->setDenormalizer($this->innerDenormalizer);

        $this->innerNormalizer = $this->createMock(NormalizerInterface::class);
        $this->normalizer->setNormalizer($this->innerNormalizer);
    }

    public function testSupportsDenormalization(): void
    {
        self::assertFalse($this->normalizer->supportsDenormalization([], \stdClass::class));
        self::assertFalse($this->normalizer->supportsDenormalization([], StepInterface::class));
        self::assertTrue($this->normalizer->supportsDenormalization([], DummyStep::class));
        self::assertFalse($this->normalizer->supportsDenormalization([], DummyStep::class, null, [
            'perform_replace' => false,
        ]));
        self::assertTrue($this->normalizer->supportsDenormalization([], DummyStep::class, null, [
            'perform_replace' => true,
        ]));
    }

    public function testSupportsNormalization(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization(new \stdClass()));
        self::assertTrue($this->normalizer->supportsNormalization($this->createMock(StepInterface::class)));
        self::assertFalse($this->normalizer->supportsNormalization(
            $this->createMock(StepInterface::class),
            null,
            [
                'ibexa.escaped_expressions' => true,
            ],
        ));
    }

    public function testEscapesExpressionsInNormalizedData(): void
    {
        $data = [
            'test' => '### "foo" ~ "test" ~ "bar" ###',
            'test1' => '###TEST1 "foo" ~ "test" ~ "bar" TEST1###',
            'test2' => '###TEST2 "foo" ~ "###TEST2" ~ "test" TEST2###',
            'test3' => '###TEST3 "foo" ~ "test" ~ "###TEST3" ~ "bar" TEST3###',
            'test_function' => '###FUNC env("APP_ENV") ~ " + foo" FUNC###',
            'test_multiple' => implode(' ', [
                '###T_M1 "foo" ~ "test" ~ "bar" T_M1###',
                '###T_M2 "foo" ~ "bar" T_M2###',
            ]),
            'test_multiple_multiline' => implode("\n", [
                '###T_M1 "foo test bar" T_M1###',
                '###T_M2 "foo" ~ "bar" T_M2###',
            ]),
        ];
        $this->innerNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->willReturn($data);

        $result = $this->normalizer->normalize($this->createMock(StepInterface::class));

        self::assertIsArray($result);
        self::assertSame([
            'test' => '\\#\\#\\# "foo" ~ "test" ~ "bar" \\#\\#\\#',
            'test1' => '\\#\\#\\#TEST1 "foo" ~ "test" ~ "bar" TEST1\\#\\#\\#',
            'test2' => '\\#\\#\\#TEST2 "foo" ~ "###TEST2" ~ "test" TEST2\\#\\#\\#',
            'test3' => '\\#\\#\\#TEST3 "foo" ~ "test" ~ "###TEST3" ~ "bar" TEST3\\#\\#\\#',
            'test_function' => '\\#\\#\\#FUNC env("APP_ENV") ~ " + foo" FUNC\\#\\#\\#',
            'test_multiple' => implode(' ', [
                '\\#\\#\\#T_M1 "foo" ~ "test" ~ "bar" T_M1\\#\\#\\#',
                '\\#\\#\\#T_M2 "foo" ~ "bar" T_M2\\#\\#\\#',
            ]),
            'test_multiple_multiline' => implode("\n", [
                '\\#\\#\\#T_M1 "foo test bar" T_M1\\#\\#\\#',
                '\\#\\#\\#T_M2 "foo" ~ "bar" T_M2\\#\\#\\#',
            ]),
        ], $result);

        $afterProcessing = $this->normalizer->denormalize($result, StepInterface::class);

        self::assertSame($data, $afterProcessing);
    }

    public function testReplacesTemplate(): void
    {
        $initialData = [
            'test' => '### "foo" ~ "test" ~ "bar" ###',
            'test1' => '###TEST1 "foo" ~ "test" ~ "bar" TEST1###',
            'test2' => '###TEST2 "foo" ~ "###TEST2" ~ "test" TEST2###',
            'test3' => '###TEST3 "foo" ~ "test" ~ "###TEST3" ~ "bar" TEST3###',
            'test_function' => '###FUNC env("APP_ENV") ~ " + foo" FUNC###',
            'test_multiple' => implode(' ', [
                '###T_M1 "foo" ~ "test" ~ "bar" T_M1###',
                '###T_M2 "foo" ~ "bar" T_M2###',
            ]),
            'test_multiple_multiline' => implode("\n", [
                '###T_M1 "foo test bar" T_M1###',
                '###T_M2 "foo" ~ "bar" T_M2###',
            ]),
            'test_reference' => 'Test string: ###REF reference("foo") REF###',
        ];

        $result = $this->normalizer->denormalize($initialData, StepInterface::class);

        self::assertSame([
            'test' => 'footestbar',
            'test1' => 'footestbar',
            'test2' => 'foo###TEST2test',
            'test3' => 'footest###TEST3bar',
            'test_function' => 'test + foo',
            'test_multiple' => 'footestbar foobar',
            'test_multiple_multiline' => "foo test bar\nfoobar",
            'test_reference' => 'Test string: "Woah! A reference!"',
        ], $result);
    }

    public function testFakerIntegration(): void
    {
        if (!class_exists(\Faker\Generator::class)) {
            $this->markTestSkipped('Faker integration not enabled');
        }

        $initialData = [
            '###TEST1 faker().name() TEST1###',
        ];

        $result = $this->normalizer->denormalize($initialData, StepInterface::class);

        self::assertArrayHasKey(0, $result);
        self::assertNotSame('###TEST1 faker().name() TEST1###', $result[0]);
        self::assertNotEmpty($result[0]);
    }

    public function testCurrentProjectDir(): void
    {
        $result = $this->normalizer->denormalize(['###TEST project_dir() TEST###'], StepInterface::class);

        self::assertArrayHasKey(0, $result);
        self::assertSame(dirname(__DIR__, 4), $result[0]);
    }

    public function testGetEnv(): void
    {
        $result = $this->normalizer->denormalize(['###TEST env("APP_ENV") TEST###'], StepInterface::class);

        self::assertIsArray($result);
        self::assertArrayHasKey(0, $result);
        self::assertSame('test', $result[0]);
    }
}
