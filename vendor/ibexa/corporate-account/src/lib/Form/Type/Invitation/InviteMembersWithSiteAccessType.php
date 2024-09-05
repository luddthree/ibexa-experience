<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Invitation;

use Ibexa\Bundle\CorporateAccount\IbexaCorporateAccountBundle;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService;
use Ibexa\CorporateAccount\Form\Data\Invitation\InviteMembersWithSiteAccessData;
use Ibexa\User\Form\Type\Invitation\SiteAccessChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InviteMembersWithSiteAccessType extends AbstractType
{
    /** @var array<string, array<string>> */
    private array $siteAccessGroups;

    private SiteAccessService $siteAccessService;

    private InviteMembersType $baseType;

    /**
     * @param array<string, array<string>> $siteAccessGroups
     */
    public function __construct(
        InviteMembersType $baseType,
        array $siteAccessGroups,
        SiteAccessService $siteAccessService
    ) {
        $this->siteAccessGroups = $siteAccessGroups;
        $this->siteAccessService = $siteAccessService;
        $this->baseType = $baseType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->baseType->buildForm($builder, $options);
        $builder->add(
            'siteAccess',
            SiteAccessChoiceType::class,
            [
                'label' => /** @Desc("Select SiteAccess") */ 'corporate_account.invite_members.site_access',
                'choices' => array_map(
                    fn (string $siteAccessName): SiteAccess => $this->siteAccessService->get($siteAccessName),
                    $this->siteAccessGroups[IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME]
                ),
            ]
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InviteMembersWithSiteAccessData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
