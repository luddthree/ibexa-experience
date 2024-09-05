<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ContentType;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\ValueObject\ContentType\Matcher;

final class RemoteLocationIdFinder implements ContentTypeFinderInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function supports($field): bool
    {
        return $field === 'location_remote_id';
    }

    public function find(Matcher $matcher): ContentType
    {
        $value = $matcher->getValue();
        assert(is_string($value));

        return $this->contentTypeService->loadContentTypeByRemoteId($value);
    }
}

class_alias(RemoteLocationIdFinder::class, 'Ibexa\Platform\Migration\StepExecutor\ContentType\RemoteLocationIdFinder');
