<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema;

use Generator;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\Iterator;
use Ibexa\GraphQL\Schema\Domain\NameValidator;

class DomainIterator implements Iterator
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var \Ibexa\GraphQL\Schema\Domain\NameValidator */
    private $nameValidator;

    public function __construct(
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        NameValidator $nameValidator
    ) {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->nameValidator = $nameValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function init(Builder $schema): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function iterate(): Generator
    {
        foreach ($this->blockDefinitionFactory->getBlockIdentifiers() as $blockIdentifier) {
            if (!$this->nameValidator->isValidName((string) $blockIdentifier)) {
                $this->nameValidator->generateInvalidNameWarning('Block identifier', (string) $blockIdentifier);

                continue;
            }

            $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockIdentifier);
            $args = ['BlockDefinition' => $blockDefinition];
            yield $args;

            foreach ($blockDefinition->getAttributes() as $attributeDefinition) {
                $args['BlockAttributeDefinition'] = $attributeDefinition;
                yield $args;
            }

            foreach ($blockDefinition->getViews() as $viewIdentifier => $view) {
                $args['BlockView'] = ['identifier' => $viewIdentifier] + $view;
                yield $args;
            }
        }
    }
}

class_alias(DomainIterator::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\DomainIterator');
