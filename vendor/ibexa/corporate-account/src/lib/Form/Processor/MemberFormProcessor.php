<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Processor;

use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\CorporateAccount\Event\DispatcherEvents;
use Ibexa\CorporateAccount\Form\Data\Member\MemberCreateData;
use Ibexa\CorporateAccount\Form\Data\Member\MemberRoleChangeData;
use Ibexa\CorporateAccount\Form\Data\Member\MemberUpdateData;
use LogicException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MemberFormProcessor extends FormProcessor
{
    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            DispatcherEvents::MEMBER_CREATE => ['processCreate', 10],
            DispatcherEvents::MEMBER_UPDATE => ['processUpdate', 10],
            DispatcherEvents::MEMBER_CHANGE_CORPORATE_ROLE => ['changeMemberRole', 10],
        ];
    }

    public function processCreate(FormActionEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data instanceof MemberCreateData) {
            return;
        }

        /** @var \Ibexa\Contracts\CorporateAccount\Values\Company $company */
        $company = $event->getOption('company');
        $languageCode = $form->getConfig()->getOption('languageCode');
        $role = $data->getRole();

        if ($role === null) {
            throw new LogicException('Creating user with role is not possible');
        }
        $memberCreateStruct = $this->memberService->newMemberCreateStruct(
            $data->login,
            $data->email,
            $data->password,
            $data->contentType
        );

        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $memberCreateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        $this->memberService->createMember(
            $company,
            $memberCreateStruct,
            $role
        );

        $redirectUrl = ($form['redirectUrlAfterPublish'] && $form['redirectUrlAfterPublish']->getData())
            ? $form['redirectUrlAfterPublish']->getData()
            : $this->urlGenerator->generate(
                'ibexa.corporate_account.company.details',
                [
                    'companyId' => $company->getId(),
                    '_fragment' => 'ibexa-tab-members',
                ]
            );

        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    public function processUpdate(FormActionEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data instanceof MemberUpdateData) {
            return;
        }

        /** @var \Ibexa\Contracts\CorporateAccount\Values\Company $company */
        $company = $event->getOption('company');
        $languageCode = $form->getConfig()->getOption('languageCode');
        $role = $data->getRole();

        $memberUpdateStruct = $this->memberService->newMemberUpdateStruct();
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $memberUpdateStruct->contentUpdateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        $member = $this->memberService->getMember($data->user->id, $company);
        $this->memberService->updateMember($member, $memberUpdateStruct);

        if ($data->getMember()->getRole()->identifier !== $data->getRole()->identifier) {
            $this->memberService->setMemberRole($data->getMember(), $data->getRole());
        }

        $redirectUrl = ($form['redirectUrlAfterPublish'] && $form['redirectUrlAfterPublish']->getData())
            ? $form['redirectUrlAfterPublish']->getData()
            : $this->urlGenerator->generate(
                'ibexa.corporate_account.company.details',
                [
                    'companyId' => $company->getId(),
                    '_fragment' => 'ibexa-tab-members',
                ]
            );

        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    public function changeMemberRole(FormActionEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof MemberRoleChangeData) {
            return;
        }

        if ($data->getMember() === null || $data->getNewRole() === null) {
            return;
        }

        if ($data->getMember()->getRole()->identifier !== $data->getNewRole()->identifier) {
            $this->memberService->setMemberRole($data->getMember(), $data->getNewRole());
        }
    }
}
