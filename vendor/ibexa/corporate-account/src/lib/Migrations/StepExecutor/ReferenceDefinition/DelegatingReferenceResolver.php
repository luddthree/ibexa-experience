<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\UnhandledReferenceDefinitionException;
use Ibexa\Migration\ValueObject\Reference\Reference;

final class DelegatingReferenceResolver implements ResolverInterface
{
    /** @var iterable<\Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface> */
    private iterable $referenceResolvers;

    /**
     * @param iterable<\Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface> $referenceResolvers
     */
    public function __construct(iterable $referenceResolvers)
    {
        $this->referenceResolvers = $referenceResolvers;
    }

    public function resolve(ReferenceDefinition $referenceDefinition, ValueObject $valueObject): Reference
    {
        $resolvableTypes = [];
        foreach ($this->referenceResolvers as $resolver) {
            try {
                return $resolver->resolve($referenceDefinition, $valueObject);
            } catch (UnhandledReferenceDefinitionException $e) {
                $resolvableTypes = [
                    ...$resolvableTypes,
                    ...$e->getResolvableTypes(),
                ];
            }
        }

        throw new UnhandledReferenceDefinitionException(
            $referenceDefinition,
            $resolvableTypes,
        );
    }
}
