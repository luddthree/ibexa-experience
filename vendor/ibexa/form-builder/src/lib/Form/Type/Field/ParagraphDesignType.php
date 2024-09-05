<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ParagraphDesignType extends AbstractFieldType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('mapped', false);
    }
}

class_alias(ParagraphDesignType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\ParagraphDesignType');
