<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeId;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Webmozart\Assert\Assert;

final class ContentTypeIdNormalizer extends AbstractCriterionNormalizer
{
    public function __construct()
    {
        parent::__construct('content_type_id');
    }

    protected function createCriterion(array $data, string $type, ?string $format, array $context): FilteringCriterion
    {
        Assert::keyExists($data, 'value');

        return new ContentTypeId($data['value']);
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ContentTypeId;
    }
}

class_alias(ContentTypeIdNormalizer::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Normalizer\Criterion\ContentTypeIdNormalizer');
