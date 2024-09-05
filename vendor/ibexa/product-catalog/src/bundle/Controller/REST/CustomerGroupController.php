<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroup;
use Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupList;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class CustomerGroupController extends RestController
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function listCustomerGroupsAction(): Value
    {
        $restCustomerGroups = [];
        $query = new CustomerGroupQuery(null, [], null);
        $customerGroups = $this->customerGroupService->findCustomerGroups($query);

        foreach ($customerGroups as $customerGroup) {
            $restCustomerGroups[] = new CustomerGroup($customerGroup);
        }

        return new CustomerGroupList($restCustomerGroups);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getCustomerGroupAction(int $id): Value
    {
        $customerGroup = $this->customerGroupService->getCustomerGroup($id);

        return new CustomerGroup($customerGroup);
    }

    public function getCustomerGroupByIdentifierAction(string $identifier): Value
    {
        $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier($identifier);

        return new CustomerGroup($customerGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createCustomerGroupAction(Request $request): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $customerGroupCreateStruct = new CustomerGroupCreateStruct(
            $input->getIdentifier(),
            $input->getNames(),
            $input->getDescriptions(),
            $input->getGlobalPriceRate()
        );

        $customerGroup = $this->customerGroupService->createCustomerGroup($customerGroupCreateStruct);

        return new CustomerGroup($customerGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateCustomerGroupAction(Request $request, int $id): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CustomerGroupUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $customerGroupUpdateStruct = new CustomerGroupUpdateStruct(
                $id,
                $input->getIdentifier(),
                $input->getNames(),
                $input->getDescriptions(),
                $input->getGlobalPriceRate()
            );

            $customerGroup = $this->customerGroupService->updateCustomerGroup($customerGroupUpdateStruct);
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        return new CustomerGroup($customerGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteCustomerGroupAction(int $id): Value
    {
        $customerGroup = $this->customerGroupService->getCustomerGroup($id);
        $this->customerGroupService->deleteCustomerGroup($customerGroup);

        return new NoContent();
    }
}
