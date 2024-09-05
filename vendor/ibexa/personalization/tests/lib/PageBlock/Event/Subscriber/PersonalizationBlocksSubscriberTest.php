<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\PageBlock\Event\Subscriber;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use Ibexa\Personalization\PageBlock\Event\Subscriber\PersonalizationBlocksSubscriber;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\PageBlock\Event\Subscriber\PersonalizationBlocksSubscriber
 */
final class PersonalizationBlocksSubscriberTest extends TestCase
{
    private PersonalizationBlocksSubscriber $pageBlockPersonalizationSubscriber;

    /** @var \Ibexa\Personalization\Security\Service\SecurityServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SecurityServiceInterface $securityService;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SettingServiceInterface $settingService;

    protected function setUp(): void
    {
        $this->securityService = $this->createMock(SecurityServiceInterface::class);
        $this->settingService = $this->createMock(SettingServiceInterface::class);
        $this->pageBlockPersonalizationSubscriber = new PersonalizationBlocksSubscriber(
            $this->securityService,
            $this->settingService
        );
    }

    public function testHidePersonalizationBlockWhenInstallationKeyIsNotSet(): void
    {
        $blockDefinitionEvent = new BlockDefinitionEvent($this->getBlockDefinition(), []);

        $this->configureSettingServiceToReturnInstallationKey(null);
        $this->pageBlockPersonalizationSubscriber->onBlockDefinition($blockDefinitionEvent);

        self::assertFalse($blockDefinitionEvent->getDefinition()->isVisible());
    }

    public function testHidePersonalizationBlockWhenCredentialsAreNotSet(): void
    {
        $blockDefinitionEvent = new BlockDefinitionEvent($this->getBlockDefinition(), []);
        $customerId = 12345;

        $this->configureSettingServiceToReturnInstallationKey('abcdefgh12345qazwsx');
        $this->mockSecurityServiceGetCurrentCustomerId($customerId);
        $this->mockSettingServiceGetLicenseKey($customerId, null);

        $this->pageBlockPersonalizationSubscriber->onBlockDefinition($blockDefinitionEvent);

        self::assertFalse($blockDefinitionEvent->getDefinition()->isVisible());
    }

    private function configureSettingServiceToReturnInstallationKey(?string $installationKey): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('getInstallationKey')
            ->willReturn($installationKey);
    }

    private function mockSecurityServiceGetCurrentCustomerId(?int $customerId): void
    {
        $this->securityService
            ->expects(self::once())
            ->method('getCurrentCustomerId')
            ->willReturn($customerId);
    }

    private function mockSettingServiceGetLicenseKey(int $customerId, ?string $licenseKey): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('getLicenceKeyByCustomerId')
            ->with($customerId)
            ->willReturn($licenseKey);
    }

    private function getBlockDefinition(): BlockDefinition
    {
        $blockDefinition = new BlockDefinition();
        $blockDefinition->setIdentifier('foo');
        $blockDefinition->setName('Foo');
        $blockDefinition->setCategory('default');
        $blockDefinition->setThumbnail('/foo/bar/block.svg');
        $blockDefinition->setVisible(true);
        $blockDefinition->setAttributes($this->getScenarioBlockAttributeDefinition());

        return $blockDefinition;
    }

    /**
     * @return array<\Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition>
     */
    private function getScenarioBlockAttributeDefinition(): array
    {
        $scenarioBlockAttributeDefinition = new BlockAttributeDefinition();
        $scenarioBlockAttributeDefinition->setIdentifier('scenario');
        $scenarioBlockAttributeDefinition->setType('select');
        $scenarioBlockAttributeDefinition->setName('Select a scenario');
        $scenarioBlockAttributeDefinition->setCategory('default');

        return [
            'scenario' => $scenarioBlockAttributeDefinition,
        ];
    }
}
