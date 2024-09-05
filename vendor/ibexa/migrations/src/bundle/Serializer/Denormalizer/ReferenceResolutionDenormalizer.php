<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ReferenceResolutionDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, ContextAwareDenormalizerInterface, LoggerAwareInterface
{
    use DenormalizerAwareTrait;
    use LoggerAwareTrait;

    private const REFERENCES_RESOLVED = 'references_resolved';

    /** @var \Ibexa\Migration\Reference\CollectorInterface */
    private $collector;

    public function __construct(CollectorInterface $collector, ?LoggerInterface $logger = null)
    {
        $this->collector = $collector;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param array<string, mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        array_walk_recursive($data, function (&$value): void {
            if (!is_string($value)) {
                return;
            }

            if (substr($value, 0, 10) !== 'reference:') {
                return;
            }

            $key = substr($value, 10);

            $value = $this->collector->getCollection()->get($key)->getValue();

            $this->getLogger()->debug(sprintf('Resolved reference "%s" to value: %s', $key, $value));
        });

        return $this->denormalizer->denormalize(
            $data,
            $type,
            $format,
            [self::REFERENCES_RESOLVED => true] + $context
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return is_subclass_of($type, StepInterface::class) && ($context[self::REFERENCES_RESOLVED] ?? false) === false;
    }
}

class_alias(ReferenceResolutionDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\ReferenceResolutionDenormalizer');
