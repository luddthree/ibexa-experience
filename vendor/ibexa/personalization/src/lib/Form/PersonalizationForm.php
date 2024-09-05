<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form;

final class PersonalizationForm
{
    public const REVENUE_FORM = 'revenue-form';
    public const CHART_FORM = 'chart-form';
    public const SCENARIO_FORM = 'scenario-form';
}

class_alias(PersonalizationForm::class, 'Ibexa\Platform\Personalization\Form\PersonalizationForm');
