<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use JMS\TranslationBundle\Annotation\Desc;

/**
 * @final
 */
class CompletenessTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/completeness.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        return [
            'product' => $contextParameters['product'],
            'completeness' => $contextParameters['completeness'],
            'language' => $contextParameters['language'],
        ];
    }

    public function getOrder(): int
    {
        return 600;
    }

    public function getIdentifier(): string
    {
        return 'completeness';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Completeness") */ 'tab.name.completeness', [], 'ibexa_product_catalog');
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return isset($parameters['completeness']);
    }
}
