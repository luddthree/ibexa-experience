<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Templating\Twig;

use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockValueConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlockValueExtension extends AbstractExtension
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockValueConverter */
    protected $blockValueConverter;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\BlockValueConverter $blockValueConverter
     */
    public function __construct(BlockValueConverter $blockValueConverter)
    {
        $this->blockValueConverter = $blockValueConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_block_value_encode',
                [$this->blockValueConverter, 'encode'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_block_value_encode',
                ]
            ),
            new TwigFunction(
                'ibexa_block_value_encode',
                [$this->blockValueConverter, 'encode']
            ),
        ];
    }
}

class_alias(BlockValueExtension::class, 'EzSystems\EzPlatformPageBuilderBundle\Templating\Twig\BlockValueExtension');
