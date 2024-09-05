<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Template;

use Ibexa\Migration\Reference\CollectorInterface;

final class ReferenceResolver
{
    /** @var \Ibexa\Migration\Reference\CollectorInterface */
    private $collector;

    public function __construct(CollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @return int|string
     */
    public function __invoke(string $referenceIdentifier)
    {
        return $this->collector->getCollection()->get($referenceIdentifier)->getValue();
    }
}
