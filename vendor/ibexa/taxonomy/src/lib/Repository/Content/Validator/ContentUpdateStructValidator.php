<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Repository\Content\Validator;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Repository\Mapper\ContentMapper;
use Ibexa\Taxonomy\Persistence\Repository\TaxonomyEntryRepository;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

/**
 * @internal
 *
 * @phpstan-import-type TValidationErrorArray from \Ibexa\Taxonomy\Repository\Content\Validator\AbstractTaxonomyIdentifierValidator
 */
final class ContentUpdateStructValidator extends AbstractTaxonomyIdentifierValidator
{
    private ContentMapper $contentMapper;

    public function __construct(
        ContentMapper $contentMapper,
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyEntryRepository $taxonomyEntryRepository
    ) {
        parent::__construct($taxonomyConfiguration, $taxonomyEntryRepository);

        $this->contentMapper = $contentMapper;
    }

    public function supports(ValueObject $object): bool
    {
        return $object instanceof ContentUpdateStruct;
    }

    /**
     * @param array<string, mixed> $context
     * @param array<string>|null $fieldIdentifiers
     *
     * @return TValidationErrorArray
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     */
    public function validate(
        ValueObject $object,
        array $context = [],
        ?array $fieldIdentifiers = null
    ): array {
        if (!$this->supports($object)) {
            throw new InvalidArgumentException('$object', 'Not supported');
        }

        if (empty($context['content']) || !$context['content'] instanceof Content) {
            throw new InvalidArgumentException('context[content]', 'Must be a ' . Content::class . ' type');
        }

        $content = $context['content'];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct $contentUpdateStruct */
        $contentUpdateStruct = $object;
        $contentType = $content->getContentType();

        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)) {
            return [];
        }

        $mainLanguageCode = $content->contentInfo->mainLanguageCode;
        $fields = $this->contentMapper->mapFieldsForUpdate(
            $contentUpdateStruct,
            $contentType,
            $mainLanguageCode
        );

        return $this->doValidate(
            $contentType,
            $mainLanguageCode,
            $fieldIdentifiers,
            $fields,
            $context
        );
    }
}
