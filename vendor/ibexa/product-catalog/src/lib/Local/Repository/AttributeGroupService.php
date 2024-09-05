<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Exception;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\View;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\HandlerInterface as AttributeHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface as AttributeGroupHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup as SPIAttributeGroup;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupCreateStruct as SPIAttributeGroupCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupUpdateStruct as SPIAttributeGroupUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroupList;
use Traversable;

final class AttributeGroupService implements LocalAttributeGroupServiceInterface
{
    private const MAX_IDENTIFIER_LENGTH = 64;
    private const MAX_NAME_LENGTH = 190;

    private Repository $repository;

    private DomainMapper $domainMapper;

    private AttributeGroupHandlerInterface $handler;

    private LanguageResolver $languageResolver;

    private LanguageHandlerInterface $languageHandler;

    private PermissionResolverInterface $permissionResolver;

    private AttributeHandlerInterface $attributeHandler;

    public function __construct(
        Repository $repository,
        DomainMapper $domainMapper,
        AttributeGroupHandlerInterface $handler,
        LanguageResolver $languageResolver,
        LanguageHandlerInterface $languageHandler,
        PermissionResolverInterface $permissionResolver,
        AttributeHandlerInterface $attributeHandler
    ) {
        $this->repository = $repository;
        $this->domainMapper = $domainMapper;
        $this->handler = $handler;
        $this->languageResolver = $languageResolver;
        $this->languageHandler = $languageHandler;
        $this->permissionResolver = $permissionResolver;
        $this->attributeHandler = $attributeHandler;
    }

    /**
     * @param iterable<int, \Ibexa\Contracts\Core\Persistence\Content\Language>|null $prioritizedLanguages
     */
    public function getAttributeGroup(string $identifier, ?iterable $prioritizedLanguages = null): AttributeGroupInterface
    {
        $this->permissionResolver->assertPolicy(new View());

        return $this->buildDomainObject(
            $this->handler->loadByIdentifier($identifier),
            $prioritizedLanguages
        );
    }

    /**
     * @internal
     */
    public function getAttributeGroupById(int $id): AttributeGroupInterface
    {
        $this->permissionResolver->assertPolicy(new View());

        return $this->buildDomainObject(
            $this->handler->load($id)
        );
    }

    public function findAttributeGroups(?AttributeGroupQuery $query = null): AttributeGroupListInterface
    {
        if (!$this->permissionResolver->canUser(new View())) {
            return new AttributeGroupList();
        }

        $query ??= new AttributeGroupQuery();

        $totalCount = $this->handler->countMatching($query->getNamePrefix());
        if ($totalCount === 0 || $query->getLimit() === 0) {
            return new AttributeGroupList([], $totalCount);
        }

        $spiAttributeGroups = $this->handler->findMatching(
            $query->getNamePrefix(),
            $query->getOffset(),
            $query->getLimit()
        );

        return new AttributeGroupList($this->buildDomainObjectList($spiAttributeGroups), $totalCount);
    }

    public function createAttributeGroup(AttributeGroupCreateStruct $createStruct): AttributeGroupInterface
    {
        $this->permissionResolver->assertPolicy(new Create($createStruct));

        $spiCreateStruct = $this->buildPersistenceCreateStruct(
            $createStruct->getIdentifier(),
            $createStruct->getNames(),
            $createStruct->getPosition()
        );

        try {
            $this->handler->loadByIdentifier($createStruct->getIdentifier());

            throw new InvalidArgumentException(
                'createStruct',
                'An Attribute Group with the provided identifier already exists'
            );
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $this->repository->beginTransaction();
        try {
            $this->handler->create($spiCreateStruct);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->buildDomainObject(
            $this->handler->loadByIdentifier($createStruct->getIdentifier())
        );
    }

    public function deleteAttributeGroup(AttributeGroupInterface $group): void
    {
        $this->permissionResolver->assertPolicy(new Delete($group));

        if ($this->isAttributeGroupUsed($group)) {
            throw new InvalidArgumentException(
                'group',
                'An Attribute Group is in use'
            );
        }

        $this->repository->beginTransaction();
        try {
            $this->handler->deleteByIdentifier($group->getIdentifier());
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    public function newAttributeGroupCreateStruct(string $identifier): AttributeGroupCreateStruct
    {
        return new AttributeGroupCreateStruct($identifier);
    }

    public function newAttributeGroupUpdateStruct(AttributeGroupInterface $group): AttributeGroupUpdateStruct
    {
        return new AttributeGroupUpdateStruct();
    }

    public function updateAttributeGroup(
        AttributeGroupInterface $attributeGroup,
        AttributeGroupUpdateStruct $updateStruct
    ): AttributeGroupInterface {
        $this->permissionResolver->assertPolicy(new Edit($updateStruct));

        if (!$attributeGroup instanceof AttributeGroup) {
            throw new InvalidArgumentType('$attributeGroup', AttributeGroup::class);
        }

        $spiUpdateStruct = $this->buildPersistenceUpdateStruct(
            $attributeGroup->getId(),
            $updateStruct->getIdentifier(),
            $updateStruct->getNames(),
            $updateStruct->getPosition()
        );

        if ($updateStruct->getIdentifier() !== null) {
            try {
                if ($attributeGroup->getId() !== $this->handler->loadByIdentifier($updateStruct->getIdentifier())->id) {
                    throw new InvalidArgumentException(
                        'updateStruct',
                        'An Attribute Group with the provided identifier already exists'
                    );
                }
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $this->repository->beginTransaction();
        try {
            $this->handler->update($spiUpdateStruct);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->buildDomainObject($this->handler->load($spiUpdateStruct->id));
    }

    public function deleteAttributeGroupTranslation(
        AttributeGroupInterface $attributeGroup,
        string $languageCode
    ): void {
        $this->permissionResolver->assertPolicy(new Delete($attributeGroup));
        $language = $this->languageHandler->loadByLanguageCode($languageCode);

        $this->repository->beginTransaction();
        try {
            $this->handler->deleteTranslation(
                $attributeGroup->getIdentifier(),
                $language->id
            );

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    public function isAttributeGroupUsed(AttributeGroupInterface $attributeGroup): bool
    {
        if (!$attributeGroup instanceof AttributeGroup) {
            throw new InvalidArgumentType('$attributeGroup', AttributeGroup::class);
        }

        return $this->attributeHandler->getProductCountByAttributeGroup($attributeGroup->getId()) > 0;
    }

    /**
     * @param iterable<int, \Ibexa\Contracts\Core\Persistence\Content\Language>|null $prioritizedLanguages
     */
    private function buildDomainObject(
        SPIAttributeGroup $spiAttributeGroup,
        ?iterable $prioritizedLanguages = null
    ): AttributeGroup {
        $prioritizedLanguages = $prioritizedLanguages ?? $this->languageHandler->loadListByLanguageCodes(
            $this->languageResolver->getPrioritizedLanguages()
        );

        return $this->domainMapper->createAttributeGroup($spiAttributeGroup, $prioritizedLanguages);
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup[] $spiAttributeGroups
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup[]
     */
    private function buildDomainObjectList(array $spiAttributeGroups): array
    {
        $prioritizedLanguages = $this->languageHandler->loadListByLanguageCodes(
            $this->languageResolver->getPrioritizedLanguages()
        );

        return $this->domainMapper->createAttributeGroupList($spiAttributeGroups, $prioritizedLanguages);
    }

    /**
     * @param array<string,string> $names
     */
    private function buildPersistenceCreateStruct(
        string $identifier,
        array $names,
        int $position
    ): SPIAttributeGroupCreateStruct {
        $languages = $this->getLanguagesMap(array_keys($names));

        $this->assertValidIdentifier($identifier);
        $this->assertValidPosition($position);
        $this->assertValidNames($names, $languages);

        $createStruct = new SPIAttributeGroupCreateStruct();
        $createStruct->identifier = $identifier;
        $createStruct->position = $position;
        $createStruct->names = [];
        foreach ($languages as $language) {
            $createStruct->names[$language->id] = $names[$language->languageCode];
        }

        return $createStruct;
    }

    /**
     * @param array<string,string>|null $names
     */
    private function buildPersistenceUpdateStruct(
        int $id,
        ?string $identifier,
        ?array $names,
        ?int $position
    ): SPIAttributeGroupUpdateStruct {
        if ($identifier !== null) {
            $this->assertValidIdentifier($identifier);
        }

        if ($position !== null) {
            $this->assertValidPosition($position);
        }

        $updateStruct = new SPIAttributeGroupUpdateStruct();
        $updateStruct->id = $id;
        $updateStruct->identifier = $identifier;
        $updateStruct->position = $position;

        if ($names !== null) {
            $languages = $this->getLanguagesMap(array_keys($names));

            $this->assertValidNames($names, $languages);

            $updateStruct->names = [];
            foreach ($languages as $language) {
                $updateStruct->names[$language->id] = $names[$language->languageCode];
            }
        }

        return $updateStruct;
    }

    /**
     * @param string[] $languageCodes
     *
     * @return array<string,\Ibexa\Contracts\Core\Persistence\Content\Language>
     */
    private function getLanguagesMap(array $languageCodes): array
    {
        $languages = $this->languageHandler->loadListByLanguageCodes($languageCodes);
        if ($languages instanceof Traversable) {
            $languages = iterator_to_array($languages);
        }

        return $languages;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertValidIdentifier(string $identifier): void
    {
        if (trim($identifier) === '' || mb_strlen($identifier) > self::MAX_IDENTIFIER_LENGTH) {
            throw new InvalidArgumentValue('identifier', $identifier);
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertValidPosition(int $position): void
    {
        if ($position < 0) {
            throw new InvalidArgumentValue('position', $position);
        }
    }

    /**
     * @param array<string, string> $names
     * @param array<string, \Ibexa\Contracts\Core\Persistence\Content\Language> $languages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertValidNames(array $names, array $languages): void
    {
        foreach ($names as $languageCode => $name) {
            if (!array_key_exists($languageCode, $languages)) {
                throw new InvalidArgumentValue('names', $names);
            }

            if (trim($name) === '' || mb_strlen($name) > self::MAX_NAME_LENGTH) {
                throw new InvalidArgumentValue('names', $names);
            }
        }
    }
}
