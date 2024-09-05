<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Symfony\Component\Validator\Constraints as Assert;

class CaptchaFieldMapper extends GenericFieldMapper
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(string $fieldIdentifier, string $formType, ConfigResolverInterface $configResolver)
    {
        parent::__construct($fieldIdentifier, $formType);
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapFormOptions(Field $field, array $constraints): array
    {
        $options = parent::mapFormOptions($field, $constraints);
        $options['field'] = $field;
        $options['label'] = $field->getName();
        if ($field->hasAttribute('help')) {
            $options['help'] = $field->getAttributeValue('help');
        }
        $options['required'] = true;
        $options['constraints'] = [
            new Assert\NotBlank(),
        ];

        if ($this->configResolver->getParameter('form_builder.captcha.use_ajax')) {
            $options['session_key'] = $field->getId();
        }

        return $options;
    }
}

class_alias(CaptchaFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\CaptchaFieldMapper');
