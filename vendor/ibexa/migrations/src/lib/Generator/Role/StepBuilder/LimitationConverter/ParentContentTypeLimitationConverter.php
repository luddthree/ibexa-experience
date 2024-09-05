<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverter;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitation;
use Ibexa\Core\Limitation\ParentContentTypeLimitationType;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Webmozart\Assert\Assert;

final class ParentContentTypeLimitationConverter implements LimitationConverterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function convertToMigration(APILimitation $limitation): Limitation
    {
        Assert::isInstanceOf($limitation, APILimitation\ParentContentTypeLimitation::class);

        $contentTypes = $this->contentTypeService->loadContentTypeList($limitation->limitationValues);
        $contentTypeIdentifiers = [];

        foreach ($contentTypes as $contentType) {
            $contentTypeIdentifiers[] = $contentType->identifier;
        }

        return new Limitation($limitation->getIdentifier(), $contentTypeIdentifiers);
    }

    public function convertToApi(Type $type, Limitation $limitation): APILimitation
    {
        Assert::isInstanceOf($type, ParentContentTypeLimitationType::class);

        $contentTypeIds = array_map(function ($value): int {
            if (is_int($value)) {
                $contentType = $this->contentTypeService->loadContentType($value);
            } elseif (is_string($value)) {
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier($value);
            } else {
                throw new NotNormalizableValueException(sprintf(
                    'Expected string or int, received %s as %s value',
                    get_debug_type($value),
                    ParentContentTypeLimitationType::class
                ));
            }

            return $contentType->id;
        }, $limitation->values);

        return $type->buildValue($contentTypeIds);
    }

    public function supportsConversionToMigration(APILimitation $limitation): bool
    {
        return $limitation instanceof APILimitation\ParentContentTypeLimitation;
    }

    public function supportsConversionToApi(Type $type, Limitation $limitation): bool
    {
        return $type instanceof ParentContentTypeLimitationType;
    }
}
