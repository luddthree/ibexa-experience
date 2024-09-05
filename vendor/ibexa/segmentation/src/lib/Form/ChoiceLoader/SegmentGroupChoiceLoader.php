<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\ChoiceLoader;

use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

final class SegmentGroupChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface */
    private $segmentationHandler;

    /** @var \Symfony\Component\Form\ChoiceList\ArrayChoiceList */
    private $choiceList;

    public function __construct(HandlerInterface $segmentationHandler)
    {
        $this->segmentationHandler = $segmentationHandler;
    }

    public function loadChoiceList(?callable $value = null): ChoiceListInterface
    {
        if (null !== $this->choiceList) {
            return $this->choiceList;
        }

        $segmentGroups = $this->segmentationHandler->loadSegmentGroups();
        $choices = array_combine(
            array_column($segmentGroups, 'name'),
            array_column($segmentGroups, 'id')
        );

        $this->choiceList = new ArrayChoiceList($choices);

        return $this->choiceList;
    }

    public function loadChoicesForValues(array $values, ?callable $value = null): array
    {
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(array $choices, ?callable $value = null): array
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

class_alias(SegmentGroupChoiceLoader::class, 'Ibexa\Platform\Segmentation\Form\ChoiceLoader\SegmentGroupChoiceLoader');
