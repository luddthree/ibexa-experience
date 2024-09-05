<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Webmozart\Assert\Assert;

final class ContentTypeGroupMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'content_type_group';
    private const WILDCARD = '*';
    private const CONTENT_TYPE_GROUP_ID = 'content_type_group_id';
    private const CONTENT_TYPE_GROUP_IDENTIFIER = 'content_type_group_identifier';

    /**
     * @var array<string, string>
     */
    private const SUPPORTED_MATCH_PROPERTY = [
        self::CONTENT_TYPE_GROUP_ID => self::CONTENT_TYPE_GROUP_ID,
        self::CONTENT_TYPE_GROUP_IDENTIFIER => self::CONTENT_TYPE_GROUP_IDENTIFIER,
    ];

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface */
    private $contentTypeGroupStepFactory;

    public function __construct(
        ContentTypeService $contentTypeService,
        StepFactoryInterface $contentTypeGroupStepFactory
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeGroupStepFactory = $contentTypeGroupStepFactory;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->contentTypeGroupStepFactory->getSupportedModes();
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable
    {
        Assert::keyExists($context, 'value');
        Assert::notEmpty($context['value']);
        Assert::keyExists($context, 'match-property');

        $matchProperty = $context['match-property'];
        $value = $context['value'];

        $contentTypeGroupsList = $this->prepareContentTypeGroupsList($matchProperty, $value);

        return $this->generateSteps($contentTypeGroupsList, $migrationMode);
    }

    /**
     * @param string|null $matchProperty
     * @param array<scalar> $value
     *
     * @return string[]
     */
    private function prepareContentTypeGroupsList(?string $matchProperty, array $value): array
    {
        if (in_array(self::WILDCARD, $value)) {
            return $this->getAllContentTypeGroupIdentifiers();
        }

        if (false === array_key_exists($matchProperty, self::SUPPORTED_MATCH_PROPERTY)) {
            throw new UnknownMatchPropertyException($matchProperty, self::SUPPORTED_MATCH_PROPERTY);
        }

        if ($matchProperty == self::CONTENT_TYPE_GROUP_ID) {
            return $this->getContentTypeGroupIdentifiersByIds($value);
        }

        $contentTypeGroupsList = $value;
        Assert::allStringNotEmpty($contentTypeGroupsList);

        return $contentTypeGroupsList;
    }

    /**
     * @return string[]
     */
    private function getAllContentTypeGroupIdentifiers(): array
    {
        $contentTypeGroupsList = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
            $contentTypeGroupsList[$group->identifier] = $group->identifier;
        }

        return $contentTypeGroupsList;
    }

    /**
     * @param array<scalar> $contentTypeGroupIds
     *
     * @return string[]
     */
    private function getContentTypeGroupIdentifiersByIds(array $contentTypeGroupIds): array
    {
        Assert::allIntegerish($contentTypeGroupIds);

        return array_map(function ($contentTypeGroupId): string {
            return $this->contentTypeService->loadContentTypeGroup((int)$contentTypeGroupId)->identifier;
        }, $contentTypeGroupIds);
    }

    /**
     * @param string[] $contentTypeGroupsList array of identifiers
     * @param \Ibexa\Migration\Generator\Mode $mode
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(array $contentTypeGroupsList, Mode $mode): iterable
    {
        foreach ($contentTypeGroupsList as $contentTypeGroupName) {
            $contentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier($contentTypeGroupName);

            yield $this->contentTypeGroupStepFactory->create($contentTypeGroup, $mode);
        }
    }
}

class_alias(ContentTypeGroupMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\ContentTypeGroup\ContentTypeGroupMigrationGenerator');
