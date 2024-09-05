<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\Generator\Mode;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class ContentDeleteDenormalizer extends AbstractDenormalizer implements DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected function getHandledKaliopType(): string
    {
        return 'content';
    }

    protected function getHandledKaliopMode(): string
    {
        return 'delete';
    }

    protected function convertFromKaliopFormat(array $data, string $type, string $format = null, array $context = []): array
    {
        return [
             'type' => 'content',
             'mode' => Mode::DELETE,
             'match' => $this->denormalizer->denormalize($data, Criterion::class, $format, $context),
         ];
    }
}

class_alias(ContentDeleteDenormalizer::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentDeleteDenormalizer');
