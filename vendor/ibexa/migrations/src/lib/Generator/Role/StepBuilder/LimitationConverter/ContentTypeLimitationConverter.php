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
use Ibexa\Core\Limitation\ContentTypeLimitationType;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Webmozart\Assert\Assert;

final class ContentTypeLimitationConverter implements LimitationConverterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function convertToMigration(APILimitation $limitation): Limitation
    {
        Assert::isInstanceOf($limitation, APILimitation\ContentTypeLimitation::class);

        $limitationValues = array_map(
            function (int $value): string {
                Assert::numeric($value, "'$value' is not numeric.");
                $contentType = $this->contentTypeService->loadContentType((int) $value);

                return $contentType->identifier;
            },
            $limitation->limitationValues
        );

        return new Limitation($limitation->getIdentifier(), $limitationValues);
    }

    public function convertToApi(Type $type, Limitation $limitation): APILimitation
    {
        Assert::isInstanceOf($type, ContentTypeLimitationType::class);

        $values = array_map(
            function (string $value): int {
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier($value);

                return $contentType->id;
            },
            $limitation->values
        );

        return $type->buildValue($values);
    }

    public function supportsConversionToMigration(APILimitation $limitation): bool
    {
        return $limitation instanceof APILimitation\ContentTypeLimitation;
    }

    public function supportsConversionToApi(Type $type, Limitation $limitation): bool
    {
        return $type instanceof ContentTypeLimitationType;
    }
}

class_alias(ContentTypeLimitationConverter::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\LimitationConverter\ContentTypeLimitationConverter');
