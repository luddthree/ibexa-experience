<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupCreateStep;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupCreateStepExecutor;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupCreateStepExecutor
 */
final class CustomerGroupCreateStepExecutorTest extends AbstractCustomerGroupExecutorTest
{
    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupCreateStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->customerGroupService = self::getServiceByClassName(CustomerGroupServiceInterface::class);
        $languageHandler = self::getServiceByClassName(LanguageHandler::class);

        $this->executor = new CustomerGroupCreateStepExecutor($this->customerGroupService, $languageHandler);
        $this->configureExecutor($this->executor);
    }

    public function testCannotHandleOtherSteps(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));
    }

    public function testHandle(): void
    {
        $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier('FOO');
        self::assertNull($customerGroup);

        $step = new CustomerGroupCreateStep(
            'FOO',
            [
                'eng-GB' => 'FOO GB name',
                'eng-US' => 'FOO US name',
                'ger-DE' => 'FOO DE name',
            ],
            [
                'eng-GB' => 'FOO GB description',
                'eng-US' => 'FOO US description',
            ],
            '20',
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        self::assertCustomerGroupsMatchTestState($this->customerGroupService);
    }
}
