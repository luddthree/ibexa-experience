<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance;

final class RecommendationDetailedReport
{
    /** @var string */
    private $name;

    /** @var string */
    private $content;

    public function __construct(
        string $name,
        string $content
    ) {
        $this->name = $name;
        $this->content = $content;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}

class_alias(RecommendationDetailedReport::class, 'Ibexa\Platform\Personalization\Value\Performance\RecommendationDetailedReport');
