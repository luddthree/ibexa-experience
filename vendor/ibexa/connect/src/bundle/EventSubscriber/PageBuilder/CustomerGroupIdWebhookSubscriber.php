<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\EventSubscriber\PageBuilder;

use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeFactoryInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CustomerGroupIdWebhookSubscriber extends AbstractEnabledQueryParameterWebhookSubscriber implements TranslationContainerInterface
{
    private CustomerGroupResolverInterface $customerGroupResolver;

    private TranslatorInterface $translator;

    public function __construct(
        BlockAttributeFactoryInterface $blockAttributeFactory,
        CustomerGroupResolverInterface $customerGroupResolver,
        TranslatorInterface $translator
    ) {
        parent::__construct($blockAttributeFactory);
        $this->translator = $translator;
        $this->customerGroupResolver = $customerGroupResolver;
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('page_builder.send_customer_group_identifier', 'ibexa_connect'))
                ->setDesc('Send Customer Group identifier'),
        ];
    }

    protected function resolveQueryParameter(): ?string
    {
        $customerGroup = $this->customerGroupResolver->resolveCustomerGroup();
        if ($customerGroup === null) {
            return null;
        }

        return $customerGroup->getIdentifier();
    }

    protected function getAttributeIdentifier(): string
    {
        return 'send_customer_group_identifier';
    }

    protected function getQueryParameterName(): string
    {
        return 'customer_group_identifier';
    }

    protected function getAttributeName(): string
    {
        return $this->translator->trans('page_builder.send_customer_group_identifier', [], 'ibexa_connect');
    }
}
