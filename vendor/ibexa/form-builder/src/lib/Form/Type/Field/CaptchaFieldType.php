<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\FormBuilder\Form\Type\AjaxCaptchaType;

class CaptchaFieldType extends AbstractFieldType
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        if ($this->configResolver->getParameter('form_builder.captcha.use_ajax')) {
            return AjaxCaptchaType::class;
        }

        return CaptchaType::class;
    }
}

class_alias(CaptchaFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\CaptchaFieldType');
