<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Search;

use Ibexa\Bundle\Personalization\DependencyInjection\Configuration\Parser\Personalization;
use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface;
use Ibexa\Personalization\Value\Search\AttributeCriteria;

final class AttributeCriteriaFactory implements AttributeCriteriaFactoryInterface
{
    private OutputTypeAttributesResolverInterface $outputTypeAttributesResolver;

    /**
     * @param \Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface $outputTypeAttributesResolver
     */
    public function __construct(OutputTypeAttributesResolverInterface $outputTypeAttributesResolver)
    {
        $this->outputTypeAttributesResolver = $outputTypeAttributesResolver;
    }

    public function getAttributesCriteria(int $customerId, string $phrase): array
    {
        $criteria = $this->getAttributesForItemTypes($customerId, $phrase);
        $criteria[] = new AttributeCriteria('contentId', $phrase);

        return $criteria;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Search\AttributeCriteria>
     */
    private function getAttributesForItemTypes(int $customerId, string $phrase): array
    {
        $outputTypeAttributes = $this->outputTypeAttributesResolver->resolve($customerId);
        $attributesName = array_unique(array_column($outputTypeAttributes, Personalization::TITLE_ATTR_NAME));
        $criteria = [];
        foreach ($attributesName as $attribute) {
            $criteria[] = new AttributeCriteria($attribute, $phrase);
        }

        return $criteria;
    }
}
