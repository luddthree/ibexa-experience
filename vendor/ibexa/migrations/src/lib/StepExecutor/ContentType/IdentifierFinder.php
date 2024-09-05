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

final class IdentifierFinder implements ContentTypeFinderInterface
{
    public const CONTENT_TYPE_IDENTIFIER = 'content_type_identifier';

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function supports($field): bool
    {
        return $field === self::CONTENT_TYPE_IDENTIFIER;
    }

    public function find(Matcher $matcher): ContentType
    {
        $value = $matcher->getValue();
        assert(is_string($value));

        return $this->contentTypeService->loadContentTypeByIdentifier($value);
    }
}

class_alias(IdentifierFinder::class, 'Ibexa\Platform\Migration\StepExecutor\ContentType\IdentifierFinder');
