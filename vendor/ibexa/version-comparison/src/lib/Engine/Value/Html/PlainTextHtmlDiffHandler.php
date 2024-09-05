<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value\Html;

use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Soundasleep\Html2Text;
use Twig\Environment;

final class PlainTextHtmlDiffHandler implements HtmlDiffHandler
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringComparisonEngine;

    /** @var \Twig\Environment */
    private $twig;

    /** @var string */
    private $template;

    /** @var string */
    private $blockName;

    public function __construct(
        StringComparisonEngine $stringComparisonEngine,
        Environment $twig,
        string $template,
        string $blockName
    ) {
        $this->stringComparisonEngine = $stringComparisonEngine;
        $this->twig = $twig;
        $this->template = $template;
        $this->blockName = $blockName;
    }

    public function getHtmlDiff(string $htmlA, string $htmlB): string
    {
        $options = ['ignore_errors' => true];
        $textA = Html2Text::convert($htmlA, $options);
        $textB = Html2Text::convert($htmlB, $options);

        $stringComparisonResult = $this->stringComparisonEngine->compareValues(
            new StringComparisonValue(['value' => $textA]),
            new StringComparisonValue(['value' => $textB]),
        );

        $template = $this->twig->load($this->template);

        return $template->renderBlock(
            $this->blockName,
            [
                'comparison_result' => $stringComparisonResult,
            ]
        );
    }
}

class_alias(PlainTextHtmlDiffHandler::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\Html\PlainTextHtmlDiffHandler');
