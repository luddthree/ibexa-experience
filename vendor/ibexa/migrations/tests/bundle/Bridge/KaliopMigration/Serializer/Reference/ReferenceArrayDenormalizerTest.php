<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceArrayDenormalizer;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use PHPUnit\Framework\TestCase;

final class ReferenceArrayDenormalizerTest extends TestCase
{
    /**
     * @dataProvider provideSamples
     */
    public function testWorksWithDifferentSyntaxes(): void
    {
        $denormalizer = new ReferenceArrayDenormalizer();

        $input = [
            '__identifier__' => '__attribute__',
        ];

        $expected = [
            [
                'name' => '__identifier__',
                'type' => '__attribute__',
            ],
        ];

        $this->assertSame($expected, $denormalizer->denormalize($input, ReferenceDefinition::class));
    }

    /**
     * @return iterable<array{
     *     'input': array<mixed>,
     *     'expected': array<mixed>,
     * }>
     */
    public function provideSamples(): iterable
    {
        yield 'short syntax' => [
            'input' => [
                '__identifier__' => '__attribute__',
            ],
            'expected' => [
                [
                    'name' => '__identifier__',
                    'type' => '__attribute__',
                ],
            ],
        ];

        yield 'long syntax' => [
            'input' => [
                [
                    'identifier' => '__identifier__',
                    'attribute' => '__attribute__',
                ],
            ],
            'expected' => [
                [
                    'name' => '__identifier__',
                    'type' => '__attribute__',
                ],
            ],
        ];

        yield 'mixed syntax' => [
            'input' => [
                '__identifier__' => '__attribute__',
                [
                    'identifier' => '__identifier__',
                    'attribute' => '__attribute__',
                ],
            ],
            'expected' => [
                [
                    'name' => '__identifier__',
                    'type' => '__attribute__',
                ],
                [
                    'name' => '__identifier__',
                    'type' => '__attribute__',
                ],
            ],
        ];
    }
}

class_alias(ReferenceArrayDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceArrayDenormalizerTest');
