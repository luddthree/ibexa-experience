<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ContentType;

use LogicException;
use Traversable;

final class ContentTypeFinderRegistry implements ContentTypeFinderRegistryInterface
{
    /** @var iterable<\Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderInterface> */
    private $finders;

    /**
     * @param iterable<\Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderInterface> $finders
     */
    public function __construct(iterable $finders)
    {
        $this->finders = $finders;
    }

    public function hasFinder($field): bool
    {
        foreach ($this->finders as $finder) {
            if ($finder->supports($field)) {
                return true;
            }
        }

        return false;
    }

    public function getFinder($field): ContentTypeFinderInterface
    {
        foreach ($this->finders as $finder) {
            if ($finder->supports($field)) {
                return $finder;
            }
        }

        $availableServices = $this->finders instanceof Traversable
            ? iterator_to_array($this->finders, false)
            : $this->finders;

        throw new LogicException(sprintf(
            'Unable to find "%s" supporting "%s" field matching. Available services: "%s". Check if your service is tagged with "%s"',
            ContentTypeFinderInterface::class,
            $field,
            implode('", "', array_map('get_class', $availableServices)),
            'ibexa.migrations.step_executor.content_type.finder',
        ));
    }
}

class_alias(ContentTypeFinderRegistry::class, 'Ibexa\Platform\Migration\StepExecutor\ContentType\ContentTypeFinderRegistry');
