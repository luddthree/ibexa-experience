<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStep;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStepExecutor;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStepExecutor
 */
final class CustomerGroupUpdateStepExecutorTest extends AbstractCustomerGroupExecutorTest
{
    private CustomerGroupServiceInterface $customerGroupService;

    private CustomerGroupUpdateStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->customerGroupService = self::getServiceByClassName(CustomerGroupServiceInterface::class);
        $languageHandler = self::getServiceByClassName(LanguageHandler::class);

        $this->executor = new CustomerGroupUpdateStepExecutor($this->customerGroupService, $languageHandler);
        $this->configureExecutor($this->executor);
    }

    public function testCannotHandleOtherSteps(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));
    }

    /**
     * @dataProvider provideForHandle
     */
    public function testHandle(CustomerGroupUpdateStep $step, callable $expectations): void
    {
        $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier('customer_group_1');
        self::assertNotNull($customerGroup);

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $expectations();
    }

    /**
     * @phpstan-return iterable<string, array{
     *     \Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStep,
     *     callable(): void,
     * }>
     */
    public function provideForHandle(): iterable
    {
        yield 'Change using "identifier" criterion' => [
            new CustomerGroupUpdateStep(
                new FieldValueCriterion('identifier', 'customer_group_1'),
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
            ),
            static function (): void {
                $service = self::getServiceByClassName(CustomerGroupServiceInterface::class);
                self::assertCustomerGroupsMatchTestState($service);
            },
        ];
    }
}
