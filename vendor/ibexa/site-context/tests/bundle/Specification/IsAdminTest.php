<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteContext\Specification;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\SiteContext\Specification\IsAdmin;
use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;
use PHPUnit\Framework\TestCase;

final class IsAdminTest extends TestCase
{
    public function testIsSatisfiedReturnsTrueForAdministrativeSiteAccess(): void
    {
        $siteAccess = $this->createMock(SiteAccess::class);
        $siteAccess->groups = [
            new SiteAccessGroup(IbexaAdminUiBundle::ADMIN_GROUP_NAME),
        ];

        $this->assertTrue((new IsAdmin())->isSatisfiedBy($siteAccess));
    }

    public function testIsSatisfiedReturnsFalseForNonAdministrativeSiteAccess(): void
    {
        $siteAccess = $this->createMock(SiteAccess::class);
        $siteAccess->groups = [
            new SiteAccessGroup('Non Admin Group'),
        ];

        $this->assertFalse((new IsAdmin())->isSatisfiedBy($siteAccess));
    }

    public function testIsSatisfiedThrowsExceptionForInvalidArgumentType(): void
    {
        $this->expectException(InvalidArgumentType::class);

        (new IsAdmin())->isSatisfiedBy('InvalidArgument');
    }
}
