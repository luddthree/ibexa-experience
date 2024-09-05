<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\BlockAttribute;

use Ibexa\Personalization\Form\DataTransformer\OutputTypeListAttributeTransformer;
use Ibexa\Personalization\Form\Type\ItemTypeChoiceType;
use Ibexa\Personalization\PageBlock\DataProvider\OutputType\OutputTypeDataProviderInterface;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\SerializerInterface;

final class OutputTypeListAttributeType extends AbstractType implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private OutputTypeDataProviderInterface $outputTypeDataProvider;

    private SecurityServiceInterface $securityService;

    private SerializerInterface $serializer;

    public function __construct(
        OutputTypeDataProviderInterface $outputTypeDataProvider,
        SecurityServiceInterface $securityService,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ) {
        $this->outputTypeDataProvider = $outputTypeDataProvider;
        $this->securityService = $securityService;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getParent(): string
    {
        return ItemTypeChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'personalization_output_type_list';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new OutputTypeListAttributeTransformer($this->serializer));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $customerId = $this->securityService->getCurrentCustomerId();
        if (null === $customerId) {
            $this->logger->warning('Customer id is not configured');

            return;
        }

        $resolver->setDefaults(
            [
                'choices' => $this->outputTypeDataProvider->getOutputTypes($customerId),
                'choice_value' => 'description',
            ]
        );
    }
}
