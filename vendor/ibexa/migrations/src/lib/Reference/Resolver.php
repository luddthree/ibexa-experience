<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

use LogicException;
use RuntimeException;

final class Resolver implements ResolverInterface
{
    /** @var CollectorInterface */
    private $collector;

    public function __construct(
        CollectorInterface $collector
    ) {
        $this->collector = $collector;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $name)
    {
        try {
            return $this->collector->getCollection()->get($name)->getValue();
        } catch (LogicException $e) {
            throw new RuntimeException(sprintf('Cannot resolve reference %s', $name), 0, $e);
        }
    }
}

class_alias(Resolver::class, 'Ibexa\Platform\Migration\Reference\Resolver');
