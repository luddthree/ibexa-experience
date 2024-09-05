<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class Resolver implements ResolverInterface
{
    /** @var \Symfony\Component\DependencyInjection\ServiceLocator */
    private $resolverRegistryByType;

    public function __construct(ServiceLocator $resolverRegistryByType)
    {
        $this->resolverRegistryByType = $resolverRegistryByType;
    }

    public function resolve(ReferenceDefinition $referenceDefinition, ValueObject $valueObject): Reference
    {
        if (false === $this->resolverRegistryByType->has($referenceDefinition->getType())) {
            throw new UnhandledReferenceDefinitionException($referenceDefinition, $this->getSupportedReferenceTypes());
        }

        /** @var \Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface $resolver */
        $resolver = $this->resolverRegistryByType->get($referenceDefinition->getType());

        return $resolver->resolve($referenceDefinition, $valueObject);
    }

    /**
     * @return string[]
     */
    private function getSupportedReferenceTypes(): array
    {
        return array_keys($this->resolverRegistryByType->getProvidedServices());
    }
}

class_alias(Resolver::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\Resolver');
