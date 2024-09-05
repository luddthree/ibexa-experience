<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\View;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroupList;

final class CustomerGroupService implements CustomerGroupServiceInterface
{
    private const VALIDATION_CUSTOMER_GROUP_IDENTIFIER_NOT_UNIQUE = 'Customer Group with identifier "%s" already exists';

    private HandlerInterface $handler;

    private PermissionResolverInterface $permissionResolver;

    private DomainMapperInterface $domainMapper;

    private LanguageHandlerInterface $languageHandler;

    private LanguageResolver $languageResolver;

    private CriterionMapper $criterionMapper;

    public function __construct(
        HandlerInterface $handler,
        PermissionResolverInterface $permissionResolver,
        DomainMapperInterface $domainMapper,
        LanguageHandlerInterface $languageHandler,
        LanguageResolver $languageResolver,
        CriterionMapper $criterionMapper
    ) {
        $this->handler = $handler;
        $this->permissionResolver = $permissionResolver;
        $this->domainMapper = $domainMapper;
        $this->criterionMapper = $criterionMapper;
        $this->languageHandler = $languageHandler;
        $this->languageResolver = $languageResolver;
    }

    public function findCustomerGroups(?CustomerGroupQuery $query = null): CustomerGroupListInterface
    {
        if (!$this->permissionResolver->canUser(new View())) {
            return new CustomerGroupList();
        }

        $query ??= new CustomerGroupQuery();
        $criteria = [];
        if ($query->getQuery() !== null) {
            $criteria[] = $this->criterionMapper->handle($query->getQuery());
        }

        $totalCount = $this->handler->countBy($criteria);
        if ($totalCount === 0) {
            return new CustomerGroupList([], $totalCount);
        }

        $customerGroups = $this->handler->findBy($criteria, [], $query->getLimit(), $query->getOffset());

        $results = [];
        foreach ($customerGroups as $customerGroup) {
            $results[] = $this->domainMapper->createFromSpi($customerGroup);
        }

        return new CustomerGroupList($results, $totalCount);
    }

    public function getCustomerGroupByIdentifier(string $identifier, ?array $prioritizedLanguages = null): ?CustomerGroupInterface
    {
        $this->permissionResolver->assertPolicy(new View());

        $result = $this->handler->findBy(['identifier' => $identifier]);

        if (empty($result)) {
            return null;
        }

        [$spiCustomerGroup] = $result;

        $spiPrioritizedLanguages = $this->getSpiPrioritizedLanguages($prioritizedLanguages);

        return $this->domainMapper->createFromSpi($spiCustomerGroup, $spiPrioritizedLanguages);
    }

    public function createCustomerGroup(CustomerGroupCreateStruct $createStruct): CustomerGroupInterface
    {
        $this->permissionResolver->assertPolicy(new Create($createStruct));

        $identifier = $createStruct->getIdentifier();
        $this->validateIdentifier($identifier);

        $id = $this->handler->create($createStruct);
        $customerGroup = $this->handler->find($id);

        return $this->domainMapper->createFromSpi($customerGroup);
    }

    public function deleteCustomerGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->permissionResolver->assertPolicy(new Delete($customerGroup));

        $this->handler->delete($customerGroup->getId());
    }

    public function deleteCustomerGroupTranslation(CustomerGroupDeleteTranslationStruct $struct): void
    {
        $this->handler->deleteTranslation($struct->getCustomerGroup(), $struct->getLanguageCode());
    }

    public function getCustomerGroup(int $id, ?array $prioritizedLanguages = null): CustomerGroupInterface
    {
        $customerGroup = $this->internalLoadCustomerGroup($id, $prioritizedLanguages);

        $this->permissionResolver->assertPolicy(new View($customerGroup));

        return $customerGroup;
    }

    public function updateCustomerGroup(CustomerGroupUpdateStruct $updateStruct): CustomerGroupInterface
    {
        $this->permissionResolver->assertPolicy(new Edit($updateStruct));

        $identifier = $updateStruct->getIdentifier();
        if ($identifier !== null) {
            $this->validateIdentifier($identifier, $updateStruct->getId());
        }

        $this->handler->update($updateStruct);

        $id = $updateStruct->getId();
        $customerGroup = $this->handler->find($id);

        return $this->domainMapper->createFromSpi($customerGroup);
    }

    /**
     * @param string[]|null $prioritizedLanguages
     */
    private function internalLoadCustomerGroup(int $id, ?array $prioritizedLanguages): CustomerGroupInterface
    {
        $spiCustomerGroup = $this->handler->find($id);

        $spiPrioritizedLanguages = $this->getSpiPrioritizedLanguages($prioritizedLanguages);

        return $this->domainMapper->createFromSpi($spiCustomerGroup, $spiPrioritizedLanguages);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function validateIdentifier(string $identifier, ?int $id = null): void
    {
        $customerGroup = $this->getCustomerGroupByIdentifier($identifier);
        if ($customerGroup !== null && $customerGroup->getId() !== $id) {
            throw new InvalidArgumentException(
                'struct',
                sprintf(self::VALIDATION_CUSTOMER_GROUP_IDENTIFIER_NOT_UNIQUE, $identifier),
            );
        }
    }

    /**
     * @param string[]|null $prioritizedLanguages
     *
     * @return iterable<\Ibexa\Contracts\Core\Persistence\Content\Language>
     */
    private function getSpiPrioritizedLanguages(?array $prioritizedLanguages): iterable
    {
        $prioritizedLanguages ??= $this->languageResolver->getPrioritizedLanguages();

        return $this->languageHandler->loadListByLanguageCodes(
            $prioritizedLanguages
        );
    }
}
