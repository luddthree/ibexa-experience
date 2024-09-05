<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Limitation;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Limitation\LimitationNormalizer;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Limitation\LimitationNormalizer
 */
final class LimitationNormalizerTest extends TestCase
{
    public function testNormalization(): void
    {
        $normalizer = new LimitationNormalizer();

        $limitation = new Limitation('__identifier__', ['__limitation_value__']);

        self::assertSame([
            'identifier' => '__identifier__',
            'values' => [
                '__limitation_value__',
            ],
        ], $normalizer->normalize($limitation));
    }

    public function testDenormalization(): void
    {
        $denormalizer = new LimitationNormalizer();
        $result = $denormalizer->denormalize(
            [
                'identifier' => '__identifier__',
                'values' => [
                    '__limitation_value__',
                ],
            ],
            Limitation::class,
        );

        self::assertInstanceOf(Limitation::class, $result);
        self::assertSame('__identifier__', $result->identifier);
        self::assertSame(['__limitation_value__'], $result->values);
    }
}

class_alias(LimitationNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Limitation\LimitationNormalizerTest');
