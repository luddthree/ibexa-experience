<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\Type\ChoiceType;

use Ibexa\Personalization\Form\Type\ChoiceType\CustomerSiteChoiceLoader;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Form\Type\ChoiceType\CustomerSiteChoiceLoader
 */
final class CustomerSiteChoiceLoaderTest extends TestCase
{
    private const GRANTED_ACCESS_LIST = [
        '123' => 'Customer 123',
        '456' => 'Customer 456',
    ];

    private CustomerSiteChoiceLoader $choiceLoader;

    protected function setUp(): void
    {
        $this->choiceLoader = new CustomerSiteChoiceLoader($this->createSecurityServiceMock());
    }

    public function testLoadChoiceList(): void
    {
        $choiceList = $this->choiceLoader->loadChoiceList();

        self::assertSame(
            [
                123 => '123',
                456 => '456',
            ],
            $choiceList->getChoices()
        );

        // loose comparison because PHP converts '123', '455' keys to integers...
        self::assertEquals(array_flip(self::GRANTED_ACCESS_LIST), $choiceList->getStructuredValues());
        self::assertSame(['456'], $choiceList->getChoicesForValues(['456']));
        self::assertSame(['123'], $choiceList->getValuesForChoices(['123']));
        self::assertEquals(array_keys(self::GRANTED_ACCESS_LIST), $choiceList->getValues());
        self::assertSame(self::GRANTED_ACCESS_LIST, $choiceList->getOriginalKeys());
    }

    private function createSecurityServiceMock(): SecurityServiceInterface
    {
        $securityService = $this->createMock(SecurityServiceInterface::class);
        $securityService->method('getGrantedAccessList')->willReturn(self::GRANTED_ACCESS_LIST);

        return $securityService;
    }
}
