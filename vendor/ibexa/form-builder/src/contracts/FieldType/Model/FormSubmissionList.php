<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FieldType\Model;

use ArrayIterator;
use IteratorAggregate;

class FormSubmissionList implements IteratorAggregate
{
    /** @var int */
    private $totalCount;

    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission[] */
    private $items;

    /** @var string[] */
    private $headers;

    /**
     * @param int $totalCount
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission[] $items
     * @param string[] $headers
     */
    public function __construct(int $totalCount = 0, array $items = [], array $headers = [])
    {
        $this->totalCount = $totalCount;
        $this->items = $items;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}

class_alias(FormSubmissionList::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Model\FormSubmissionList');
