<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\CriterionGenerator;

use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use InvalidArgumentException;

final class GeneratorRegistry implements GeneratorRegistryInterface
{
    /** @var array<string, \Ibexa\Migration\Generator\CriterionGenerator\GeneratorInterface> */
    private $generators = [];

    /**
     * @param iterable<\Ibexa\Migration\Generator\CriterionGenerator\GeneratorInterface> $generators
     */
    public function __construct(iterable $generators)
    {
        foreach ($generators as $key => $generator) {
            if (!is_string($key)) {
                throw new InvalidArgumentException(sprintf(
                    'Expected a string value, received %s. Ensure that services injected are tagged with "key" '
                    . 'attribute (see https://symfony.com/doc/current/service_container/tags.html#tagged-services-with-index)',
                    $key,
                ));
            }

            $this->generators[$key] = $generator;
        }
    }

    public function find(string $matchProperty): GeneratorInterface
    {
        if (!isset($this->generators[$matchProperty])) {
            throw new UnknownMatchPropertyException($matchProperty, array_keys($this->generators));
        }

        return $this->generators[$matchProperty];
    }
}

class_alias(GeneratorRegistry::class, 'Ibexa\Platform\Migration\Generator\CriterionGenerator\GeneratorRegistry');
