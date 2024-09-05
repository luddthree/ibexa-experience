<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST;

use Ibexa\Contracts\Rest\FieldTypeProcessor;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductSpecificationFieldTypeProcessor extends FieldTypeProcessor implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private NormalizerInterface $normalizer;

    public function __construct(
        NormalizerInterface $normalizer,
        ?LoggerInterface $logger = null
    ) {
        $this->normalizer = $normalizer;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Normalizes objects into their scalar representation.
     *
     * Warning: while normal fields are always arrays, default values might be null.
     *
     * @phpstan-param array{id: int, code: string, attributes: array<mixed>}|null $outgoingValueHash
     *
     * @phpstan-return array{id: int, code: string, attributes: array<scalar>|scalar}|null
     */
    public function postProcessValueHash($outgoingValueHash): ?array
    {
        if (isset($outgoingValueHash['attributes'])) {
            $outgoingValueHash = $this->handleAttributes($outgoingValueHash);
        }

        return parent::postProcessValueHash($outgoingValueHash);
    }

    /**
     * @phpstan-param array{id: int, code: string, attributes: array<mixed>} $outgoingValueHash
     *
     * @phpstan-return array{id: int, code: string, attributes: array<scalar>|scalar}
     */
    private function handleAttributes(array $outgoingValueHash): array
    {
        foreach ($outgoingValueHash['attributes'] as $key => $attribute) {
            try {
                $outgoingValueHash['attributes'][$key] = $this->normalizer->normalize($attribute);
            } catch (ExceptionInterface $e) {
                $message = sprintf(
                    'Unable to normalize value for type "%s" at product attribute with key: "%s". %s.'
                    . 'Ensure that a normalizer is registered with tag: "%s".',
                    get_debug_type($attribute),
                    $key,
                    $e->getMessage(),
                    'ibexa.rest.serializer.normalizer',
                );
                $this->logger->error($message, [
                    'exception' => $e,
                ]);
                $outgoingValueHash['attributes'][$key] = [];
            }
        }

        return $outgoingValueHash;
    }
}
