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
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Create;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\View;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\HandlerInterface as AttributeHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition as SPIAttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionCreateStruct as SPIAttributeDefinitionCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionUpdateStruct as SPIAttributeDefinitionUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorDispatcher;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionList;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Traversable;

final class AttributeDefinitionService implements LocalAttributeDefinitionServiceInterface
{
    private const MAX_IDENTIFIER_LENGTH = 64;
    private const MAX_NAME_LENGTH = 190;
    private const MAX_DESCRIPTION_LENGTH = 10000;

    private Repository $repository;

    private DomainMapper $domainMapper;

    private AttributeDefinitionHandlerInterface $handler;

    private LanguageHandlerInterface $languageHandler;

    private LanguageResolver $languageResolver;

    private PermissionResolverInterface $permissionResolver;

    private OptionsValidatorDispatcher $optionsValidatorDispatcher;

    private AttributeHandlerInterface $attributeHandler;

    public function __construct(
        Repository $repository,
        DomainMapper $domainMapper,
        AttributeDefinitionHandlerInterface $handler,
        LanguageHandlerInterface $languageHandler,
        LanguageResolver $languageResolver,
        PermissionResolverInterface $permissionResolver,
        OptionsValidatorDispatcher $optionsValidatorDispatcher,
        AttributeHandlerInterface $attributeHandler
    ) {
        $this->repository = $repository;
        $this->domainMapper = $domainMapper;
        $this->handler = $handler;
        $this->languageHandler = $languageHandler;
        $this->languageResolver = $languageResolver;
        $this->permissionResolver = $permissionResolver;
        $this->optionsValidatorDispatcher = $optionsValidatorDispatcher;
        $this->attributeHandler = $attributeHandler;
    }

    /**
     * @param iterable<string>|null $prioritizedLanguages
     */
    public function getAttributeDefinition(string $identifier, ?iterable $prioritizedLanguages = null): AttributeDefinitionInterface
    {
        $this->permissionResolver->assertPolicy(new View());

        return $this->buildDomainObject(
            $this->handler->loadByIdentifier($identifier),
            $prioritizedLanguages
        );
    }

    public function findAttributesDefinitions(?AttributeDefinitionQuery $query = null): AttributeDefinitionListInterface
    {
        if (!$this->permissionResolver->canUser(new View())) {
            return new AttributeDefinitionList();
        }

        $query ??= new AttributeDefinitionQuery();

        $totalCount = $this->handler->countMatching($query);
        if ($totalCount === 0 || $query->getLimit() === 0) {
            return new AttributeDefinitionList([], $totalCount);
        }

        $spiAttributeDefinitions = $this->handler->findMatching($query);

        return new AttributeDefinitionList($this->buildDomainObjectList($spiAttributeDefinitions), $totalCount);
    }

    public function createAttributeDefinition(
        AttributeDefinitionCreateStruct $createStruct
    ): AttributeDefinitionInterface {
        $this->permissionResolver->assertPolicy(new Create($createStruct));

        $spiCreateStruct = $this->buildPersistenceCreateStruct($createStruct);

        try {
            $this->handler->loadByIdentifier($createStruct->getIdentifier());

            throw new InvalidArgumentException(
                'createStruct',
                'An Attribute Definition with the provided identifier already exists'
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

    public function deleteAttributeDefinition(AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->permissionResolver->assertPolicy(new Delete($attributeDefinition));

        if ($this->isAttributeDefinitionUsed($attributeDefinition)) {
            throw new InvalidArgumentException(
                'attributeDefinition',
                'An Attribute Definition is in use'
            );
        }

        $this->handler->deleteByIdentifier($attributeDefinition->getIdentifier());
    }

    public function newAttributeDefinitionCreateStruct(string $identifier): AttributeDefinitionCreateStruct
    {
        return new AttributeDefinitionCreateStruct($identifier);
    }

    public function newAttributeDefinitionUpdateStruct(
        AttributeDefinitionInterface $attributeDefinition
    ): AttributeDefinitionUpdateStruct {
        return new AttributeDefinitionUpdateStruct();
    }

    public function updateAttributeDefinition(
        AttributeDefinitionInterface $attributeDefinition,
        AttributeDefinitionUpdateStruct $updateStruct
    ): AttributeDefinitionInterface {
        $this->permissionResolver->assertPolicy(new Edit($updateStruct));

        if (!$attributeDefinition instanceof AttributeDefinition) {
            throw new InvalidArgumentType('$attributeDefinition', AttributeDefinition::class);
        }

        $spiUpdateStruct = $this->buildPersistenceUpdateStruct($attributeDefinition, $updateStruct);

        if ($updateStruct->getIdentifier() !== null) {
            try {
                if ($attributeDefinition->getId() !== $this->handler->loadByIdentifier($updateStruct->getIdentifier())->id) {
                    throw new InvalidArgumentException(
                        'updateStruct',
                        'An Attribute Definition with the provided identifier already exists'
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

    public function deleteAttributeDefinitionTranslation(
        AttributeDefinitionInterface $attributeDefinition,
        string $languageCode
    ): void {
        $this->permissionResolver->assertPolicy(new Delete($attributeDefinition));
        $language = $this->languageHandler->loadByLanguageCode($languageCode);

        $this->repository->beginTransaction();
        try {
            $this->handler->deleteTranslation(
                $attributeDefinition->getIdentifier(),
                $language->id
            );

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    public function isAttributeDefinitionUsed(AttributeDefinitionInterface $attributeDefinition): bool
    {
        if (!$attributeDefinition instanceof AttributeDefinition) {
            throw new InvalidArgumentType('$attributeDefinition', AttributeDefinition::class);
        }

        return $this->attributeHandler->getProductCount($attributeDefinition->getId()) > 0;
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition[] $spiAttributeDefinitions
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition[]
     */
    private function buildDomainObjectList(array $spiAttributeDefinitions): array
    {
        $prioritizedLanguages = $this->languageHandler->loadListByLanguageCodes(
            $this->languageResolver->getPrioritizedLanguages()
        );

        $items = [];
        foreach ($spiAttributeDefinitions as $spiAttributeDefinition) {
            $items[] = $this->domainMapper->createAttributeDefinition($spiAttributeDefinition, $prioritizedLanguages);
        }

        return $items;
    }

    /**
     * @param iterable<string>|null $prioritizedLanguages
     */
    private function buildDomainObject(
        SPIAttributeDefinition $spiAttributeDefinition,
        ?iterable $prioritizedLanguages = null
    ): AttributeDefinition {
        /** @var string[]|null $prioritizedLanguages */
        $prioritizedLanguages ??= $this->languageResolver->getPrioritizedLanguages();
        $spiPrioritizedLanguages = $this->languageHandler->loadListByLanguageCodes(
            $prioritizedLanguages
        );

        return $this->domainMapper->createAttributeDefinition($spiAttributeDefinition, $spiPrioritizedLanguages);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function buildPersistenceCreateStruct(
        AttributeDefinitionCreateStruct $createStruct
    ): SPIAttributeDefinitionCreateStruct {
        if (!$createStruct->getGroup() instanceof AttributeGroup) {
            throw new InvalidArgumentType('group', AttributeGroup::class);
        }

        $languages = $this->getLanguagesMap(array_keys($createStruct->getNames()));

        $names = $createStruct->getNames();
        $descriptions = $createStruct->getDescriptions();

        $this->assertValidIdentifier($createStruct->getIdentifier());
        $this->assertValidPosition($createStruct->getPosition());
        $this->assertValidOptions($createStruct->getType(), $createStruct->getOptions());
        $this->assertValidNames($names, $languages);
        $this->assertValidDescriptions($descriptions, $languages);

        $spiCreateStruct = new SPIAttributeDefinitionCreateStruct();
        $spiCreateStruct->identifier = $createStruct->getIdentifier();
        $spiCreateStruct->type = $createStruct->getType()->getIdentifier();
        $spiCreateStruct->attributeGroupId = $createStruct->getGroup()->getId();
        $spiCreateStruct->position = $createStruct->getPosition();
        $spiCreateStruct->options = $createStruct->getOptions();

        foreach ($languages as $language) {
            $spiCreateStruct->names[$language->id] = $names[$language->languageCode];

            if (isset($descriptions[$language->languageCode])) {
                $spiCreateStruct->descriptions[$language->id] = $descriptions[$language->languageCode];
            }
        }

        return $spiCreateStruct;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function buildPersistenceUpdateStruct(
        AttributeDefinition $attributeDefinition,
        AttributeDefinitionUpdateStruct $updateStruct
    ): SPIAttributeDefinitionUpdateStruct {
        if (!$updateStruct->getGroup() instanceof AttributeGroup) {
            throw new InvalidArgumentType('group', AttributeGroup::class);
        }

        if ($updateStruct->getIdentifier() !== null) {
            $this->assertValidIdentifier($updateStruct->getIdentifier());
        }

        if ($updateStruct->getPosition() !== null) {
            $this->assertValidPosition($updateStruct->getPosition());
        }

        $names = $updateStruct->getNames();
        $descriptions = $updateStruct->getDescriptions();
        $languages = $this->getLanguagesMap(array_keys($names));

        $this->assertValidNames($names, $languages);
        $this->assertValidDescriptions($descriptions, $languages);
        $this->assertValidOptions($attributeDefinition->getType(), $updateStruct->getOptions());

        $spiUpdateStruct = new SPIAttributeDefinitionUpdateStruct();
        $spiUpdateStruct->id = $attributeDefinition->getId();
        $spiUpdateStruct->attributeGroupId = ($updateStruct->getGroup() ?? $attributeDefinition->getGroup())->getId();
        $spiUpdateStruct->identifier = $updateStruct->getIdentifier() ?? $attributeDefinition->getIdentifier();
        $spiUpdateStruct->type = $attributeDefinition->getType()->getIdentifier();
        $spiUpdateStruct->position = $updateStruct->getPosition() ?? $attributeDefinition->getPosition();
        $spiUpdateStruct->options = $updateStruct->getOptions();

        foreach ($languages as $language) {
            $spiUpdateStruct->names[$language->id] = $names[$language->languageCode];

            if (isset($descriptions[$language->languageCode])) {
                $spiUpdateStruct->descriptions[$language->id] = $descriptions[$language->languageCode];
            }
        }

        return $spiUpdateStruct;
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

    /**
     * @param array<string, string> $descriptions
     * @param array<string, \Ibexa\Contracts\Core\Persistence\Content\Language> $languages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertValidDescriptions(array $descriptions, array $languages): void
    {
        foreach ($descriptions as $languageCode => $description) {
            if (!array_key_exists($languageCode, $languages)) {
                throw new InvalidArgumentValue('descriptions', $descriptions);
            }

            if (mb_strlen($description) > self::MAX_DESCRIPTION_LENGTH) {
                throw new InvalidArgumentValue('descriptions', $descriptions);
            }
        }
    }

    /**
     * @param array<string,mixed> $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function assertValidOptions(AttributeTypeInterface $type, ?array $options): void
    {
        if ($options !== null) {
            $errors = $this->optionsValidatorDispatcher->validateOptions($type, $options);
            if (!empty($errors)) {
                throw new InvalidArgumentValue('options', $options);
            }
        }
    }
}
