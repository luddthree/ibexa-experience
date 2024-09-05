<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Webmozart\Assert\Assert;

final class ContentIdNormalizer extends AbstractCriterionNormalizer
{
    public function __construct()
    {
        parent::__construct('content_id');
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     */
    protected function createCriterion(array $data, string $type, ?string $format, array $context): FilteringCriterion
    {
        Assert::keyExists($data, 'value');

        return new ContentId($data['value']);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ContentId;
    }
}

class_alias(ContentIdNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Criterion\ContentIdNormalizer');
