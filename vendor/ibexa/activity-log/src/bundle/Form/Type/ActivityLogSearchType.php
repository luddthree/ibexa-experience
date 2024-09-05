<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Form\Type;

use DateTimeImmutable;
use Ibexa\ActivityLog\Permission\ActivityLogOwnerLimitation;
use Ibexa\ActivityLog\Permission\PolicyProvider;
use Ibexa\Bundle\ActivityLog\Form\Data\ActivityLogSearchData;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ActivityLogSearchType extends AbstractType
{
    private PermissionResolver $permissionResolver;

    public function __construct(
        PermissionResolver $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('query', TextType::class, [
            'required' => false,
        ]);

        if (!$this->hasOwnLogsOnlyLimitation()) {
            $builder->add('users', ActivityLogUserChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ]);
        }

        $builder->add('objectClasses', ObjectClassChoiceType::class, [
            'required' => false,
            'multiple' => true,
        ]);

        $builder->add('actions', ActionChoiceType::class, [
            'required' => false,
            'multiple' => true,
        ]);

        $builder->add('time', ChoiceType::class, [
            'choices' => [
                /** @Desc("Any time (max. 30 days)") */
                'ibexa.activity_log.search_form.time.any' => null,
                /** @Desc("Last week") */
                'ibexa.activity_log.search_form.time.last_week' => new DateTimeImmutable('-7 days'),
                /** @Desc("Last 24h") */
                'ibexa.activity_log.search_form.time.last_24_hours' => new DateTimeImmutable('-24 hours'),
            ],
            'translation_domain' => 'ibexa_activity_log',
            'block_prefix' => 'ibexa_activity_log_time_choice',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActivityLogSearchData::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'activity_log_search';
    }

    private function hasOwnLogsOnlyLimitation(): bool
    {
        $access = $this->permissionResolver->hasAccess(PolicyProvider::MODULE_ACTIVITY_LOG, 'read');

        if (!is_array($access)) {
            return false;
        }

        foreach ($access as $accessData) {
            if (!isset($accessData['policies'])) {
                continue;
            }

            foreach ($accessData['policies'] as $policy) {
                foreach ($policy->getLimitations() as $limitation) {
                    if ($limitation instanceof ActivityLogOwnerLimitation) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
