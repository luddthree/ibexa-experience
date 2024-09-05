<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\PercentEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\AttributeSubtask;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AttributesTask extends AbstractTask
{
    private const ATTRIBUTE_GROUP_KEY_SEPARATOR = '-';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getIdentifier(): string
    {
        return 'attributes';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Attributes") */ 'product.completeness.attributes.label', [], 'ibexa_product_catalog');
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        return new PercentEntry($this->getTaskCompletenessValue($product));
    }

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array
    {
        $definitionIdentifiers = [];
        $groupedSubtasks = [];
        $subtaskGroups = [];

        foreach ($this->getApplicableAttributesDefinitions($product) as $definition) {
            $definitionIdentifiers[] = $definition->getAttributeDefinition()->getIdentifier();
        }

        foreach ($product->getAttributes() as $attribute) {
            if (!in_array($attribute->getIdentifier(), $definitionIdentifiers, true)) {
                continue;
            }

            $attributeGroup = $attribute->getAttributeDefinition()->getGroup();
            $attributeGroupKey =
                $attributeGroup->getIdentifier() .
                self::ATTRIBUTE_GROUP_KEY_SEPARATOR .
                $attributeGroup->getName();

            $groupedSubtasks[$attributeGroupKey][] = new AttributeSubtask($attribute);
        }

        foreach ($groupedSubtasks as $key => $subtasks) {
            [$groupIdentifier, $groupName] = explode(self::ATTRIBUTE_GROUP_KEY_SEPARATOR, $key, 2);
            $subtaskGroups[] = new TaskGroup($groupIdentifier, $groupName, $product, $subtasks);
        }

        usort(
            $subtaskGroups,
            static function (TaskGroup $groupA, TaskGroup $groupB): int {
                return strcmp($groupA->getIdentifier(), $groupB->getIdentifier());
            }
        );

        return $subtaskGroups;
    }

    /**
     * @phpstan-return int<1, max>
     */
    public function getWeight(): int
    {
        return 1;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface>
     */
    private function getApplicableAttributesDefinitions(ProductInterface $product): iterable
    {
        $definitions = $product->getProductType()->getAttributesDefinitions();
        $isBaseProduct = $product->isBaseProduct();

        foreach ($definitions as $definition) {
            if (!$isBaseProduct) {
                yield $definition;

                continue;
            }

            if (!$definition->isDiscriminator() && !$definition->isRequired()) {
                yield $definition;
            }
        }
    }

    public function getEditUrl(ProductInterface $product): ?string
    {
        return null;
    }
}
