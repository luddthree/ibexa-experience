<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Workflow\ChoiceLoader;

use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

abstract class ChoiceLoader implements ChoiceLoaderInterface
{
    protected CorporateAccountConfiguration $configuration;

    public function __construct(
        CorporateAccountConfiguration $configuration
    ) {
        $this->configuration = $configuration;
    }

    abstract public function loadChoiceList(callable $value = null): ChoiceListInterface;

    /**
     * @param string[] $values
     * @param mixed $value
     *
     * @return array<mixed>
     */
    public function loadChoicesForValues(array $values, $value = null): array
    {
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * @return string[]
     */
    public function loadValuesForChoices(array $choices, $value = null): array
    {
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
