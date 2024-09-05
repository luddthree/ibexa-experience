<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read string $identifier
 * @property-read string $name
 * @property-read array<string, string> $names
 * @property-read string $languageCode
 * @property-read string $mainLanguageCode
 * @property-read \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $parent
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
 */
class TaxonomyEntryUpdateStruct extends ValueObject
{
    protected string $identifier;

    protected string $name;

    /**
     * @var array<string, string>
     */
    protected array $names;

    protected string $languageCode;

    protected string $mainLanguageCode;

    protected ?TaxonomyEntry $parent;

    protected Content $content;

    /**
     * @param array<string, string> $names
     */
    public function __construct(
        string $identifier,
        string $name,
        array $names,
        string $languageCode,
        string $mainLanguageCode,
        ?TaxonomyEntry $parent,
        Content $content
    ) {
        parent::__construct([
            'identifier' => $identifier,
            'name' => $name,
            'names' => $names,
            'languageCode' => $languageCode,
            'mainLanguageCode' => $mainLanguageCode,
            'parent' => $parent,
            'content' => $content,
        ]);
    }
}
