<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer\Content\Action;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Content\Action\AssignSectionActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignSection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\Content\Action\AssignSectionActionDenormalizer
 */
final class AssignSectionActionDenormalizerTest extends TestCase
{
    /**
     * @dataProvider provideForThrowsWhenInvalidMatchingPropertyProvided
     *
     * @param array<string, scalar> $data
     */
    public function testThrowsWhenInvalidMatchingPropertyProvided(array $data, string $expectedExceptionMessage): void
    {
        $denormalizer = new AssignSectionActionDenormalizer();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $denormalizer->denormalize($data, AssignSection::TYPE);
    }

    /**
     * @return iterable<array{array<string, scalar>, string}>
     */
    public function provideForThrowsWhenInvalidMatchingPropertyProvided(): iterable
    {
        yield [
            [
                'action' => AssignSection::TYPE,
            ],
            sprintf('Unable to denormalize "%s" action: missing matching property (one of "id", "identifier")', AssignSection::TYPE),
        ];

        yield [
            [
                'action' => AssignSection::TYPE,
                'id' => 1,
                'identifier' => 'foo',
            ],
            sprintf('Unable to denormalize "%s" action: more than one matching property provided (received: "id", "identifier")', AssignSection::TYPE),
        ];
    }
}
