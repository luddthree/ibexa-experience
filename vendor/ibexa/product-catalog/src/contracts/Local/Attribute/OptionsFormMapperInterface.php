<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Attribute;

use Symfony\Component\Form\FormBuilderInterface;

interface OptionsFormMapperInterface
{
    /**
     * @param array<string,mixed> $context
     */
    public function createOptionsForm(
        string $name,
        FormBuilderInterface $builder,
        array $context = []
    ): void;
}
