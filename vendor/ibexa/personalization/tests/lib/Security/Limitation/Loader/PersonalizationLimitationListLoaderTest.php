<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Security\Limitation\Loader;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoader;
use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use PHPUnit\Framework\TestCase;

final class PersonalizationLimitationListLoaderTest extends TestCase
{
    /** @var \Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private PersonalizationLimitationListLoaderInterface $PersonalizationLimitationListLoader;

    /** @var \Ibexa\Personalization\SiteAccess\ScopeParameterResolver|\PHPUnit\Framework\MockObject\MockObject */
    private ScopeParameterResolver $scopeParameterResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessService;

    /** @var string[] */
    private array $customerIdList;

    public function setUp(): void
    {
        parent::setUp();

        $this->PersonalizationLimitationListLoader = $this->createMock(PersonalizationLimitationListLoaderInterface::class);
        $this->scopeParameterResolver = $this->createMock(ScopeParameterResolver::class);
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->customerIdList = [
            1234 => '1234: Test instance',
        ];
    }

    public function testCreateInstancePersonalizationLimitationListLoader(): void
    {
        self::assertInstanceOf(
            PersonalizationLimitationListLoaderInterface::class,
            new PersonalizationLimitationListLoader(
                $this->siteAccessService,
                $this->scopeParameterResolver
            ),
        );
    }

    public function testGetList(): void
    {
        $this->PersonalizationLimitationListLoader
            ->method('getList')
            ->willReturn($this->customerIdList);

        self::assertEquals(
            [
                1234 => '1234: Test instance',
            ],
            $this->PersonalizationLimitationListLoader->getList()
        );
    }
}

class_alias(PersonalizationLimitationListLoaderTest::class, 'Ibexa\Platform\Tests\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderTest');
