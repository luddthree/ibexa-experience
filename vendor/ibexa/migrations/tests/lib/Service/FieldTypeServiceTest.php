<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Service;

use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Migration\Service\FieldTypeService;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class FieldTypeServiceTest extends TestCase
{
    use ExpectDeprecationTrait;

    private const FIELD_TYPE_IDENTIFIER = 'foo_field_type';

    private FieldTypeService $fieldTypeService;

    /** @var \Ibexa\Contracts\Core\FieldType\FieldType&\PHPUnit\Framework\MockObject\MockObject */
    private FieldType $fieldTypeMock;

    protected function setUp(): void
    {
        $fieldTypeRegistryMock = $this->createMock(FieldTypeRegistry::class);
        $this->fieldTypeMock = $this->createMock(FieldType::class);
        $fieldTypeRegistryMock
            ->method('getFieldType')
            ->with(self::FIELD_TYPE_IDENTIFIER)
            ->willReturn($this->fieldTypeMock)
        ;
        $this->fieldTypeService = new FieldTypeService(
            $fieldTypeRegistryMock,
            $this->createMock(EventDispatcherInterface::class)
        );
    }

    /**
     * @return iterable<string, array{array<mixed>|null, array<mixed>|null}>
     */
    public function getDataForTestGetFieldSettingsFromHash(): iterable
    {
        $settingValue = new stdClass();
        $settingValue->value = 'value1';
        $fieldSettings = ['setting1' => $settingValue];

        yield 'simple setting hash' => [
            ['setting1' => 'value1'],
            $fieldSettings,
        ];

        yield 'null (no settings)' => [
            null,
            null,
        ];
    }

    /**
     * @dataProvider getDataForTestGetFieldSettingsFromHash
     *
     * @param array<mixed>|null $fieldSettingsHash
     * @param array<mixed>|null $fieldSettings
     */
    public function testGetFieldSettingsFromHash(?array $fieldSettingsHash, ?array $fieldSettings): void
    {
        $this->fieldTypeMock
            ->method('fieldSettingsFromHash')
            ->with($fieldSettingsHash)
            ->willReturn($fieldSettings)
        ;

        self::assertSame(
            $fieldSettings,
            $this->fieldTypeService->getFieldSettingsFromHash($fieldSettingsHash, self::FIELD_TYPE_IDENTIFIER)
        );
    }

    /**
     * @dataProvider getDataForTestGetFieldSettingsFromHash
     *
     * @param array<mixed>|null $fieldSettingsHash
     * @param array<mixed>|null $fieldSettings
     */
    public function testGetFieldSettingsToHash(?array $fieldSettingsHash, ?array $fieldSettings): void
    {
        $this->fieldTypeMock
            ->method('fieldSettingsToHash')
            ->with($fieldSettings)
            ->willReturn($fieldSettingsHash)
        ;

        self::assertSame(
            $fieldSettingsHash,
            $this->fieldTypeService->getFieldSettingsToHash($fieldSettings, self::FIELD_TYPE_IDENTIFIER)
        );
    }

    /**
     * @group legacy
     */
    public function testGetFieldSettingsFromHashTriggersDeprecation(): void
    {
        $this->configureDeprecationExpectation('fieldSettingsFromHash');

        $this->fieldTypeService->getFieldSettingsFromHash(1, self::FIELD_TYPE_IDENTIFIER);
    }

    /**
     * @group legacy
     */
    public function testGetFieldSettingsToHashTriggersDeprecation(): void
    {
        $this->configureDeprecationExpectation('fieldSettingsToHash');

        $this->fieldTypeService->getFieldSettingsToHash(1, self::FIELD_TYPE_IDENTIFIER);
    }

    private function configureDeprecationExpectation(string $coreFieldTypeMethod): void
    {
        $this->fieldTypeMock
            ->method($coreFieldTypeMethod)
            ->with(1)
            ->willReturn(1)
        ;

        $this->expectDeprecation(
            sprintf(
                'Since ibexa/migrations 4.5: Returning non-hash field settings by %s::%s ' .
                'for "%s" field type is deprecated and will result in a fatal error in 5.0',
                get_class($this->fieldTypeMock),
                $coreFieldTypeMethod,
                self::FIELD_TYPE_IDENTIFIER
            )
        );
    }
}
