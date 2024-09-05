<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Factory\Export;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\Config\CredentialsResolverInterface;
use Ibexa\Personalization\Exception\InvalidArgumentException;
use Ibexa\Personalization\Factory\Export\ParametersFactory;
use Ibexa\Personalization\Factory\Export\ParametersFactoryInterface;
use Ibexa\Personalization\Value\Config\PersonalizationClientCredentials;
use Ibexa\Personalization\Value\Export\Parameters;
use PHPUnit\Framework\TestCase;

final class ParametersFactoryTest extends TestCase
{
    private const ITEMS_ENDPOINT = 'https://link.invalid';

    private ParametersFactoryInterface $parametersFactory;

    /** @var \Ibexa\Personalization\Config\CredentialsResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CredentialsResolverInterface $credentialsResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessService;

    /**
     * @phpstan-var array{
     *  customer_id: string,
     *  license_key: string,
     *  item_type_identifier_list: string,
     *  languages: string,
     *  siteaccess: string,
     *  web_hook: string,
     *  host: string,
     *  page_size: string,
     * }
     */
    private array $options;

    public function setUp(): void
    {
        $this->credentialsResolver = $this->createMock(CredentialsResolverInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->parametersFactory = new ParametersFactory(
            $this->credentialsResolver,
            $this->configResolver,
            $this->siteAccessService,
            self::ITEMS_ENDPOINT
        );
        $this->options = [
            'customer_id' => '12345',
            'license_key' => '12345-12345-12345-12345',
            'siteaccess' => 'test',
            'item_type_identifier_list' => 'article, product, blog',
            'languages' => 'eng-GB',
            'web_hook' => 'https://link.invalid/api/12345/items',
            'host' => 'https://127.0.0.1',
            'page_size' => '500',
        ];
    }

    /**
     * @throws \Ibexa\Personalization\Exception\MissingExportParameterException
     */
    public function testCreateFromAllOptions(): void
    {
        $this->configureSiteAccessServiceToReturnAllSiteAccesses();

        self::assertEquals(
            Parameters::fromArray($this->options),
            $this->parametersFactory->create($this->options, ParametersFactoryInterface::COMMAND_TYPE)
        );
    }

    public function testCreateWithAutocomplete(): void
    {
        $siteAccess = 'test';
        $options = [
            'customer_id' => null,
            'license_key' => null,
            'siteaccess' => null,
            'item_type_identifier_list' => 'article, product, blog',
            'languages' => 'eng-GB',
            'page_size' => '500',
            'web_hook' => null,
            'host' => null,
        ];

        $this->credentialsResolver
            ->expects(self::atLeastOnce())
            ->method('hasCredentials')
            ->with($siteAccess)
            ->willReturn(true);

        $this->siteAccessService
            ->expects(self::atLeastOnce())
            ->method('getAll')
            ->willReturn(
                [
                    new SiteAccess($siteAccess),
                ]
            );

        $this->credentialsResolver
            ->expects(self::once())
            ->method('getCredentials')
            ->with($siteAccess)
            ->willReturn(new PersonalizationClientCredentials(
                12345,
                '12345-12345-12345-12345'
            ));

        $this->configureConfigResolverToReturnHostUri($siteAccess);

        self::assertEquals(
            Parameters::fromArray($this->options),
            $this->parametersFactory->create($options, ParametersFactoryInterface::COMMAND_TYPE)
        );
    }

    public function testCreateForSingleConfiguration(): void
    {
        $siteAccess = 'test';
        $options = [
            'customer_id' => '12345',
            'license_key' => '12345-12345-12345-12345',
            'siteaccess' => $siteAccess,
            'item_type_identifier_list' => 'article, product, blog',
            'languages' => 'eng-GB',
            'page_size' => '500',
            'web_hook' => null,
            'host' => null,
        ];

        $this->siteAccessService
            ->method('getAll')
            ->willReturn(
                [
                    new SiteAccess($siteAccess),
                ]
            );

        $this->credentialsResolver
            ->expects(self::once())
            ->method('hasCredentials')
            ->with($siteAccess)
            ->willReturn(true);

        $this->configureConfigResolverToReturnHostUri($siteAccess);

        self::assertEquals(
            Parameters::fromArray($this->options),
            $this->parametersFactory->create($options, ParametersFactoryInterface::COMMAND_TYPE)
        );
    }

    public function testCreateForMultiCustomerConfiguration(): void
    {
        $firstSiteAccess = 'test';
        $secondSiteAccess = 'second_siteaccess';

        $options = [
            'customer_id' => '12345',
            'license_key' => '12345-12345-12345-12345',
            'siteaccess' => $firstSiteAccess,
            'item_type_identifier_list' => 'article, product, blog',
            'languages' => 'eng-GB',
            'page_size' => '500',
            'web_hook' => null,
            'host' => null,
        ];

        $this->configureSiteAccessServiceToReturnAllSiteAccesses();

        $this->credentialsResolver
            ->expects(self::at(0))
            ->method('hasCredentials')
            ->with($firstSiteAccess)
            ->willReturn(true);

        $this->credentialsResolver
            ->expects(self::at(1))
            ->method('hasCredentials')
            ->with($secondSiteAccess)
            ->willReturn(true);

        $this->configureConfigResolverToReturnHostUri($firstSiteAccess);

        self::assertEquals(
            Parameters::fromArray($this->options),
            $this->parametersFactory->create($options, ParametersFactoryInterface::COMMAND_TYPE)
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\MissingExportParameterException
     */
    public function testThrowExportCredentialsNotFoundException(): void
    {
        $siteAccess = 'invalid';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('SiteAccess %s doesn\'t exists', $siteAccess));

        $this->configureSiteAccessServiceToReturnAllSiteAccesses();

        $this->parametersFactory->create(
            [
                'customer_id' => null,
                'license_key' => null,
                'siteaccess' => $siteAccess,
                'item_type_identifier_list' => 'article, product, blog',
                'languages' => 'eng-GB',
                'page_size' => '500',
                'web_hook' => null,
                'host' => null,
            ],
            ParametersFactoryInterface::COMMAND_TYPE
        );
    }

    private function configureSiteAccessServiceToReturnAllSiteAccesses(): void
    {
        $this->siteAccessService
            ->method('getAll')
            ->willReturn(
                [
                    new SiteAccess('test'),
                    new SiteAccess('second_siteaccess'),
                ]
            );
    }

    private function configureConfigResolverToReturnHostUri(string $siteAccess): void
    {
        $this->configResolver
            ->expects(self::atLeastOnce())
            ->method('getParameter')
            ->with(
                'personalization.host_uri',
                null,
                $siteAccess
            )
            ->willReturn('https://127.0.0.1');
    }
}
