<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\AttributeGroupFetchAdapter;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Webmozart\Assert\Assert;

final class AttributeGroupMigrationGenerator implements MigrationGeneratorInterface
{
    private StepFactoryInterface $stepBuilderFactory;

    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(
        StepFactoryInterface $stepBuilderFactory,
        AttributeGroupServiceInterface $attributeGroupService
    ) {
        $this->stepBuilderFactory = $stepBuilderFactory;
        $this->attributeGroupService = $attributeGroupService;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return 'attribute_group';
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

        if ($context['match-property'] !== null) {
            if ($context['match-property'] !== 'identifier') {
                throw new \LogicException(sprintf(
                    'Unable to match by "%s" property. Only matching by "%s" identifier is supported.',
                    $context['match-property'],
                    'identifier',
                ));
            }

            $value = $context['value'];
            if (!is_array($value)) {
                $value = [$value];
            }

            $attributeGroups = [];
            foreach ($value as $v) {
                $attributeGroups[] = $this->attributeGroupService->getAttributeGroup((string)$v);
            }
        } else {
            $adapter = new AttributeGroupFetchAdapter(
                $this->attributeGroupService,
            );
            $attributeGroups = new BatchIterator($adapter);
        }

        foreach ($attributeGroups as $attributeGroup) {
            Assert::isInstanceOf($attributeGroup, AttributeGroup::class);

            yield $this->stepBuilderFactory->create($attributeGroup, $migrationMode);
        }
    }
}
