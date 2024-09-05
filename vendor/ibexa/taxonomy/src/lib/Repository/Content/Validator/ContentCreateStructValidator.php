<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Repository\Content\Validator;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
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
final class ContentCreateStructValidator extends AbstractTaxonomyIdentifierValidator
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
        return $object instanceof ContentCreateStruct;
    }

    /**
     * @param array<string, mixed> $context
     * @param array<string>|null $fieldIdentifiers
     *
     * @return TValidationErrorArray
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyNotFoundException
     */
    public function validate(
        ValueObject $object,
        array $context = [],
        ?array $fieldIdentifiers = null
    ): array {
        if (!$this->supports($object)) {
            throw new InvalidArgumentException('$object', 'Not supported');
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct */
        $contentCreateStruct = $object;
        $contentType = $contentCreateStruct->contentType;

        if (!$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentType)) {
            return [];
        }

        return $this->doValidate(
            $contentType,
            $contentCreateStruct->mainLanguageCode,
            $fieldIdentifiers,
            $this->contentMapper->mapFieldsForCreate($contentCreateStruct),
            $context
        );
    }
}
