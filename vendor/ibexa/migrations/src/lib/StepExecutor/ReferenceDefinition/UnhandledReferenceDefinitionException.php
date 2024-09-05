<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use RuntimeException;

final class UnhandledReferenceDefinitionException extends RuntimeException
{
    /** @var string[] */
    private array $resolvableTypes;

    /**
     * @param string[] $resolvableTypes
     */
    public function __construct(ReferenceDefinition $referenceDefinition, array $resolvableTypes)
    {
        $this->resolvableTypes = $resolvableTypes;
        $message = sprintf(
            'Unable to handle reference definition (named "%s"). "%s" is not one of possible types: "%s"',
            $referenceDefinition->getName(),
            $referenceDefinition->getType(),
            implode('", "', $this->resolvableTypes),
        );

        parent::__construct($message);
    }

    /**
     * @return string[]
     */
    public function getResolvableTypes(): array
    {
        return $this->resolvableTypes;
    }
}

class_alias(UnhandledReferenceDefinitionException::class, 'Ibexa\Platform\Migration\StepExecutor\ReferenceDefinition\UnhandledReferenceDefinitionException');
