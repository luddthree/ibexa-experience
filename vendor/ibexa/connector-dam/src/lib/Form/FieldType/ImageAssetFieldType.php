<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Form\FieldType;

use Ibexa\ContentForms\ConfigResolver\MaxUploadSize;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ImageAssetFieldType extends AbstractType
{
    /** @var \Symfony\Component\Form\AbstractType */
    private $innerType;

    /** @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    /** @var \Ibexa\ContentForms\ConfigResolver\MaxUploadSize */
    private $maxUploadSize;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configurationResolver;

    public function __construct(
        AbstractType $innerType,
        AssetMapper $assetMapper,
        MaxUploadSize $maxUploadSize,
        ConfigResolverInterface $configurationResolver
    ) {
        $this->innerType = $innerType;
        $this->assetMapper = $assetMapper;
        $this->maxUploadSize = $maxUploadSize;
        $this->configurationResolver = $configurationResolver;
    }

    public function getBlockPrefix()
    {
        return $this->innerType->getBlockPrefix();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->innerType->buildForm($builder, $options);

        $builder
            ->add('source', HiddenType::class)
            ->add('destinationContentId', HiddenType::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($view->vars['value']['source'] === null) {
            $this->innerType->buildView($view, $form, $options);
        }
        $view->vars['max_file_size'] = $this->getMaxFileSize();
        $enabledConnectors = $this->configurationResolver->getParameter('content.dam');
        $view->vars['dam_connector_enabled'] = !empty($enabledConnectors);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_content_forms_fieldtype',
            'source' => null,
        ]);
    }

    private function getMaxFileSize(): float
    {
        $validatorConfiguration = $this->assetMapper
            ->getAssetFieldDefinition()
            ->getValidatorConfiguration();

        $maxFileSize = $validatorConfiguration['FileSizeValidator']['maxFileSize'];
        if ($maxFileSize > 0) {
            return (float)min($maxFileSize * 1024 * 1024, $this->maxUploadSize->get());
        }

        return (float)$this->maxUploadSize->get();
    }
}

class_alias(ImageAssetFieldType::class, 'Ibexa\Platform\Connector\Dam\Form\FieldType\ImageAssetFieldType');
