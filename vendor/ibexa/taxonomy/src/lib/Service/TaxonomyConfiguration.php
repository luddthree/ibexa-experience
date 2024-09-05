<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Exception\TaxonomyConfigurationNotFoundException;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;

class TaxonomyConfiguration
{
    public const CONFIG_CONTENT_TYPE = 'content_type';
    public const CONFIG_FIELD_MAPPING = 'field_mappings';
    public const CONFIG_PARENT_LOCATION_REMOTE_ID = 'parent_location_remote_id';
    public const CONFIG_REGISTER_MAIN_MENU = 'register_main_menu';
    public const CONFIG_ASSIGNED_CONTENT_TAB = 'assigned_content_tab';

    private const DEFAULT_TAXONOMY_NAME = 'tags';

    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        array $config
    ) {
        $this->config = $config;
    }

    /**
     * @return array<string, string>
     */
    public function getFieldMappings(string $taxonomy): array
    {
        return $this->getConfigForTaxonomy($taxonomy, self::CONFIG_FIELD_MAPPING);
    }

    public function getTaxonomyForContentType(ContentType $contentType): string
    {
        $contentTypeIdentifier = $contentType->identifier;

        foreach ($this->config as $taxonomy => $config) {
            if ($config[self::CONFIG_CONTENT_TYPE] === $contentTypeIdentifier) {
                return $taxonomy;
            }
        }

        throw new TaxonomyNotFoundException(
            sprintf(
                'Content type %s (%s) is not associated with any taxonomy',
                $contentType->getName(),
                $contentTypeIdentifier
            )
        );
    }

    /**
     * @return mixed
     */
    public function getConfigForTaxonomy(string $taxonomy, string $name)
    {
        if (!isset($this->config[$taxonomy])) {
            throw TaxonomyNotFoundException::createWithTaxonomyName($taxonomy);
        }

        if (!isset($this->config[$taxonomy][$name])) {
            throw TaxonomyConfigurationNotFoundException::create($taxonomy, $name);
        }

        return $this->config[$taxonomy][$name];
    }

    /**
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyConfigurationNotFoundException
     */
    public function getDefaultTaxonomyName(): string
    {
        $taxonomies = $this->getTaxonomies();
        if (in_array(self::DEFAULT_TAXONOMY_NAME, $taxonomies, true)) {
            return self::DEFAULT_TAXONOMY_NAME;
        }

        $taxonomyName = reset($taxonomies);

        if ($taxonomyName === false) {
            throw new TaxonomyConfigurationNotFoundException('No taxonomies configured.');
        }

        return $taxonomyName;
    }

    /**
     * @return string[]
     */
    public function getTaxonomies(): array
    {
        return array_keys($this->config);
    }

    public function getEntryIdentifierFieldFromContent(Content $content): string
    {
        $field = $this->getFieldFromContent('identifier', $content);

        return (string) $field->value;
    }

    public function getEntryParentFieldFromContent(Content $content): ?TaxonomyEntry
    {
        $field = $this->getFieldFromContent('parent', $content);

        if (null === $field) {
            return null;
        }

        return $field->value->getTaxonomyEntry();
    }

    public function isContentTypeAssociatedWithTaxonomy(ContentType $contentType): bool
    {
        try {
            $this->getTaxonomyForContentType($contentType);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    private function getFieldFromContent(string $field, Content $content): ?Field
    {
        $taxonomy = $this->getTaxonomyForContentType($content->getContentType());
        $mappings = $this->getFieldMappings($taxonomy);

        return $content->getField($mappings[$field]);
    }
}
