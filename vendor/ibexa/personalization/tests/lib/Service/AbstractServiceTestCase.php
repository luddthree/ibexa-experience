<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service;

use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractServiceTestCase extends TestCase
{
    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $settingService;

    public function setUp(): void
    {
        parent::setUp();

        $this->settingService = $this->createMock(SettingServiceInterface::class);
    }

    protected function getLicenseKey(?string $siteAccess = null): void
    {
        $this->settingService
            ->method('getLicenceKeyByCustomerId')
            ->willReturn('12345-12345-12345-12345');
    }
}

class_alias(AbstractServiceTestCase::class, 'Ibexa\Platform\Tests\Personalization\Service\AbstractServiceTestCase');
