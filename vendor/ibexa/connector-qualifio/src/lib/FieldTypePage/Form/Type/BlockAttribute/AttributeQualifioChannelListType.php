<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\FieldTypePage\Form\Type\BlockAttribute;

use DateTimeInterface;
use Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeQualifioChannelListType extends AbstractType
{
    private QualifioServiceInterface $qualifioService;

    public function __construct(
        QualifioServiceInterface $qualifioService
    ) {
        $this->qualifioService = $qualifioService;
    }

    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_qualifio_channel_list';
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $convertToObject = static function (?int $campaignId): ?QualifioChannel {
            if ($campaignId === null) {
                return null;
            }

            return new QualifioChannel($campaignId);
        };

        $convertToValue = static function (?QualifioChannel $channel): ?int {
            if ($channel === null) {
                return null;
            }

            return $channel->getCampaignId();
        };

        if ($options['multiple'] === false) {
            $builder->addModelTransformer(
                new CallbackTransformer(
                    $convertToObject,
                    $convertToValue,
                ),
            );

            return;
        }

        $builder->addModelTransformer(
            new CallbackTransformer(
                static fn (array $data): array => array_map($convertToObject, $data),
                static fn (array $data): array => array_map($convertToValue, $data),
            ),
        );

        $builder->addModelTransformer(
            new CallbackTransformer(
                static fn ($input): array => explode(',', $input ?? ''),
                static fn ($input): string => implode(',', $input ?? []),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::lazy(
                $this,
                function (): array {
                    $qualifioChannels = $this->qualifioService->getQualifioChannels();
                    $data = [];

                    foreach ($qualifioChannels as $qualifioChannelData) {
                        $qualifioChannel = QualifioChannel::createFromArray($qualifioChannelData);
                        $data[$qualifioChannel->getCampaignId()] = $qualifioChannel;
                    }

                    return $data;
                },
                [],
            ),
            'choice_label' => static function (?QualifioChannel $qualifioChannel): ?string {
                if ($qualifioChannel === null) {
                    return null;
                }

                return $qualifioChannel->getCampaignTitle();
            },
            'choice_value' => static function (?QualifioChannel $qualifioChannel): ?int {
                if ($qualifioChannel === null) {
                    return null;
                }

                return $qualifioChannel->getCampaignId();
            },
            'choice_attr' => static function (?QualifioChannel $qualifioChannel): array {
                if ($qualifioChannel === null) {
                    return [];
                }

                $startDate = $qualifioChannel->getStartDate();
                $endDate = $qualifioChannel->getEndDate();

                if ($startDate instanceof DateTimeInterface) {
                    $startDate = $startDate->format(DATE_W3C);
                }

                if ($endDate instanceof DateTimeInterface) {
                    $endDate = $endDate->format(DATE_W3C);
                }

                return [
                    'data-website-name' => $qualifioChannel->getWebsiteName(),
                    'data-campaign-title' => $qualifioChannel->getCampaignTitle(),
                    'data-campaign-type' => $qualifioChannel->getCampaignType(),
                    'data-start-date' => $startDate,
                    'data-end-date' => $endDate,
                ];
            },
            'group_by' => static function (?QualifioChannel $qualifioChannel): ?string {
                if ($qualifioChannel === null) {
                    return null;
                }

                return $qualifioChannel->getWebsiteName();
            },
        ]);
    }
}
