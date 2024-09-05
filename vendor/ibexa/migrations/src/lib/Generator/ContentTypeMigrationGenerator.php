<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Generator\StepBuilder\ContentTypeStepFactory;
use Webmozart\Assert\Assert;

final class ContentTypeMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'content_type';
    private const WILDCARD = '*';
    private const SUPPORTED_MATCH_PROPERTY = [
        'content_type_identifier' => true,
    ];

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Migration\Generator\StepBuilder\ContentTypeStepFactory */
    private $contentTypeStepFactory;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentTypeStepFactory $contentTypeStepFactory
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeStepFactory = $contentTypeStepFactory;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && \in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    public function getSupportedModes(): array
    {
        return $this->contentTypeStepFactory->getSupportedModes();
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

        $contentTypesList = $this->prepareContentTypesList($matchProperty, $value);

        return $this->generateSteps($contentTypesList, $migrationMode);
    }

    /**
     * @param array<string|int> $value
     *
     * @return array<string>
     */
    private function prepareContentTypesList(?string $matchProperty, array $value): array
    {
        if (in_array(self::WILDCARD, $value)) {
            return $this->getAllContentTypeIdentifiers();
        }

        if (false === array_key_exists($matchProperty, self::SUPPORTED_MATCH_PROPERTY)) {
            throw new UnknownMatchPropertyException(
                (string)$matchProperty,
                array_keys(self::SUPPORTED_MATCH_PROPERTY)
            );
        }

        $contentTypesList = $value;
        Assert::allStringNotEmpty($contentTypesList);

        return $contentTypesList;
    }

    /**
     * @return array<string>
     */
    private function getAllContentTypeIdentifiers(): array
    {
        $contentTypesList = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
            foreach ($this->contentTypeService->loadContentTypes($group) as $contentType) {
                $contentTypesList[$contentType->identifier] = $contentType->identifier;
            }
        }

        return $contentTypesList;
    }

    /**
     * @param string[] $contentTypesList array of identifiers
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(array $contentTypesList, Mode $migrationMode): iterable
    {
        foreach ($contentTypesList as $contentTypeName) {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeName);

            yield $this->contentTypeStepFactory->create($contentType, $migrationMode);
        }
    }
}

class_alias(ContentTypeMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\ContentTypeMigrationGenerator');
