<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Security\Limitation\Mapper;

use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface;
use Ibexa\Personalization\Security\Limitation\Mapper\PersonalizationAccessLimitationMapper;
use Ibexa\Personalization\Value\Limitation\PersonalizationAccessLimitation;
use PHPUnit\Framework\TestCase;

final class PersonalizationAccessLimitationMapperTest extends TestCase
{
    /** @var \Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private PersonalizationLimitationListLoaderInterface $limitationListLoader;

    /** @var string[] */
    private array $customerIdList;

    public function setUp(): void
    {
        parent::setUp();

        $this->limitationListLoader = $this->createMock(PersonalizationLimitationListLoaderInterface::class);
        $this->customerIdList = [
            '1234' => '1234 First Test Account',
            '5678' => '5678 Second Test Account',
        ];
    }

    public function testMapLimitationValue(): void
    {
        $limitation = new PersonalizationAccessLimitation(
            [
                'limitationValues' => ['1234', '5678'],
            ]
        );

        $this->limitationListLoader
            ->method('getList')
            ->willReturn([
                '1234' => '1234 First Test Account',
                '5678' => '5678 Second Test Account',
            ]);

        $mapper = new PersonalizationAccessLimitationMapper($this->limitationListLoader);
        $result = $mapper->mapLimitationValue($limitation);

        self::assertEquals(array_values($this->customerIdList), $result);
    }

    public function testGetSelectionChoices(): void
    {
        $this->limitationListLoader
            ->method('getList')
            ->willReturn([
                '1234' => '1234 First Test Account',
                '5678' => '5678 Second Test Account',
            ]);

        self::assertEquals($this->customerIdList, $this->limitationListLoader->getList());
    }
}

class_alias(PersonalizationAccessLimitationMapperTest::class, 'Ibexa\Platform\Tests\Personalization\Security\Limitation\Mapper\PersonalizationAccessLimitationMapperTest');
