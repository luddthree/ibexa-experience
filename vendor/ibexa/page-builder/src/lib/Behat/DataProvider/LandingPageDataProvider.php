<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Behat\DataProvider;

use Ibexa\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;

final class LandingPageDataProvider implements FieldTypeDataProviderInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Type */
    private $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === 'ezlandingpage';
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return new Value($this->createPageWithCodeBlock('<h1>Test header</h1>'));
    }

    public function parseFromString(string $value)
    {
        return new Value($this->createPageWithCodeBlock($value));
    }

    private function createPageWithCodeBlock($blockContent): Page
    {
        $page = $this->type->getEmptyValue()->getPage();
        $zones = $page->getZones();
        $zones[0]->addBlock($this->getCodeBlockData($blockContent));
        $page->setZones($zones);

        return $page;
    }

    private function getCodeBlockData(string $content): BlockValue
    {
        return new BlockValue(
            uniqid('', true),
            'tag',
            'Code',
            'default',
            null,
            null,
            '',
            null,
            null,
            [new Attribute(
                uniqid('', true),
                'content',
                $content
            )]
        );
    }
}
