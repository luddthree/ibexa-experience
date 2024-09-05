<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Form\Extension;

use Ibexa\ContentForms\Form\Type\FieldType\BinaryBaseFieldType;
use Ibexa\Contracts\ImageEditor\Optimizer\OptimizerInterface;
use Ibexa\ImageEditor\File\Base64FileTransformer;
use Ibexa\ImageEditor\Form\EventListener\Base64FileUploadedSubscriber;
use Ibexa\ImageEditor\Form\EventListener\ImageOptimizerSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UploadedBase64File extends AbstractTypeExtension
{
    /** @var \Ibexa\ImageEditor\File\Base64FileTransformer */
    private $fileTransformer;

    /** @var \Ibexa\Contracts\ImageEditor\Optimizer\OptimizerInterface */
    private $optimizer;

    public function __construct(
        Base64FileTransformer $fileTransformer,
        OptimizerInterface $optimizer
    ) {
        $this->fileTransformer = $fileTransformer;
        $this->optimizer = $optimizer;
    }

    public static function getExtendedTypes(): iterable
    {
        return [BinaryBaseFieldType::class];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'file_name' => null,
            'use_base64' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['use_base64']) {
            return;
        }

        $builder
            ->add('base64', HiddenType::class)
            ->add(
                'fileName',
                HiddenType::class,
                [
                    'data' => $options['file_name'],
                ]
            );

        $builder->addEventSubscriber(
            new Base64FileUploadedSubscriber(
                $this->fileTransformer
            )
        );
        $builder->addEventSubscriber(
            new ImageOptimizerSubscriber(
                $this->optimizer
            )
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['use_base64'] = $options['use_base64'];
    }
}

class_alias(UploadedBase64File::class, 'Ibexa\Platform\ImageEditor\Form\Extension\UploadedBase64File');
