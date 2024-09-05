<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Component;

use Ibexa\AdminUi\Component\TwigComponent;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Twig\Environment;

final class AttributeDefinitionOptionsComponent extends TwigComponent
{
    private string $attributeType;

    /**
     * @param array<string,mixed> $parameters
     */
    public function __construct(
        Environment $twig,
        string $attributeType,
        string $template,
        array $parameters = []
    ) {
        parent::__construct($twig, $template, $parameters);

        $this->attributeType = $attributeType;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        $definition = $parameters['attribute_definition'] ?? null;
        if ($definition instanceof AttributeDefinitionInterface && $this->isSupported($definition)) {
            return parent::render($parameters);
        }

        return '';
    }

    private function isSupported(AttributeDefinitionInterface $definition): bool
    {
        return $definition->getType()->getIdentifier() === $this->attributeType;
    }
}
