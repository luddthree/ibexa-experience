<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentFieldPreparationTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentFieldPreparationTrait
 */
final class ContentFieldPreparationTraitTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentFieldPreparationTrait|object */
    private $contentFieldConversion;

    protected function setUp(): void
    {
        $this->contentFieldConversion = new class() {
            use ContentFieldPreparationTrait {
                prepareFields as public convertFields;
            }
        };
    }

    public function testChecksThatAllFieldsArePrefixedWithLanguage(): void
    {
        self::assertSame(
            [
                [
                    'fieldDefIdentifier' => 'name',
                    'languageCode' => null,
                    'value' => 'Foo',
                ], [
                    'fieldDefIdentifier' => 'title',
                    'languageCode' => null,
                    'value' => 'Bar',
                ], [
                    'fieldDefIdentifier' => 'hash field',
                    'languageCode' => null,
                    'value' => ['Foo'],
                ],
            ],
            $this->contentFieldConversion->convertFields([
                'attributes' => [
                    'name' => 'Foo',
                    'title' => 'Bar',
                    'hash field' => ['Foo'],
                ],
            ]),
        );
    }

    public function testWorksWithFieldsPrefixedWithLanguage(): void
    {
        self::assertSame(
            [
                [
                    'fieldDefIdentifier' => 'name',
                    'languageCode' => 'eng-GB',
                    'value' => 'Foo',
                ], [
                    'fieldDefIdentifier' => 'title',
                    'languageCode' => 'eng-GB',
                    'value' => 'Bar',
                ], [
                    'fieldDefIdentifier' => 'hash field',
                    'languageCode' => 'eng-GB',
                    'value' => ['Foo'],
                ],
            ],
            $this->contentFieldConversion->convertFields([
                'attributes' => [
                    'name' => [
                        'eng-GB' => 'Foo',
                    ],
                    'title' => [
                        'eng-GB' => 'Bar',
                    ],
                    'hash field' => [
                        'eng-GB' => ['Foo'],
                    ],
                ],
            ]),
        );
    }

    public function testWorksWithFieldsWithScalarValues(): void
    {
        self::assertSame(
            [
                [
                    'fieldDefIdentifier' => 'name',
                    'languageCode' => null,
                    'value' => 'Foo',
                ], [
                    'fieldDefIdentifier' => 'title',
                    'languageCode' => null,
                    'value' => 'Bar',
                ], [
                    'fieldDefIdentifier' => 'hash field',
                    'languageCode' => null,
                    'value' => 42,
                ],
            ],
            $this->contentFieldConversion->convertFields([
                'attributes' => [
                    'name' => 'Foo',
                    'title' => 'Bar',
                    'hash field' => 42,
                ],
            ]),
        );
    }
}

class_alias(ContentFieldPreparationTraitTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentFieldPreparationTraitTest');
