<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\IndexTemplate;

use ArrayIterator;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Iterator;

final class IndexTemplateRegistry implements IndexTemplateRegistryInterface
{
    /** @var array */
    private $templates;

    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    public function getIndexTemplate(string $name): array
    {
        if ($this->hasIndexTemplate($name)) {
            return $this->templates[$name];
        }

        throw new InvalidArgumentException('$name', "No index template found with name $name");
    }

    public function hasIndexTemplate(string $name): bool
    {
        return isset($this->templates[$name]);
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->templates);
    }
}

class_alias(IndexTemplateRegistry::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\IndexTemplate\IndexTemplateRegistry');
