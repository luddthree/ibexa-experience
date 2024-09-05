<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Webmozart\Assert\Assert;

final class AttributeMigrationGenerator implements MigrationGeneratorInterface
{
    private StepFactoryInterface $stepBuilderFactory;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        StepFactoryInterface $stepBuilderFactory,
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->stepBuilderFactory = $stepBuilderFactory;
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return 'attribute';
    }

    public function getSupportedModes(): array
    {
        return $this->stepBuilderFactory->getSupportedModes();
    }

    public function generate(Mode $migrationMode, array $context): iterable
    {
        Assert::keyExists($context, 'value');
        Assert::notEmpty($context['value']);
        Assert::keyExists($context, 'match-property');

        $query = null;
        if (isset($context['match-property']) && $context['value'] !== ['*']) {
            $query = new AttributeDefinitionQuery(
                new FieldValueCriterion($context['match-property'], $context['value']),
                [],
                null,
            );
        }

        foreach ($this->attributeDefinitionService->findAttributesDefinitions($query) as $attribute) {
            Assert::isInstanceOf($attribute, AttributeDefinition::class);

            yield $this->stepBuilderFactory->create($attribute, $migrationMode);
        }
    }
}
