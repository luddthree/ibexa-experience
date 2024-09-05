<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type;

use Gregwar\CaptchaBundle\Type\CaptchaType;

final class AjaxCaptchaType extends CaptchaType
{
    public function getBlockPrefix(): string
    {
        return 'ajax_captcha';
    }
}

class_alias(AjaxCaptchaType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\AjaxCaptchaType');
