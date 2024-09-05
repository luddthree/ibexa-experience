<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\CriterionGenerator;

/**
 * Registry containing CriterionGenerator\GeneratorInterface services.
 *
 * @internal
 */
interface GeneratorRegistryInterface
{
    /**
     * @throws \Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException
     */
    public function find(string $matchProperty): GeneratorInterface;
}

class_alias(GeneratorRegistryInterface::class, 'Ibexa\Platform\Migration\Generator\CriterionGenerator\GeneratorRegistryInterface');
