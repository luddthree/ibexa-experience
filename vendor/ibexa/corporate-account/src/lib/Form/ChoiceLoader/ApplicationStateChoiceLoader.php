<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\ChoiceLoader;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

final class ApplicationStateChoiceLoader implements ChoiceLoaderInterface, TranslationContainerInterface
{
    private const TRANSLATION_DOMAIN = 'ibexa_corporate_account_applications';

    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    /**
     * @return array<string, string>
     */
    private function getChoiceList(): array
    {
        /** @var string[] $states */
        $states = $this->configResolver->getParameter('corporate_account.application.states');

        $choices = [];

        foreach ($states as $state) {
            $name = 'application.state.' . $state;
            $choices[$name] = $state;
        }

        return $choices;
    }

    public function loadChoiceList(callable $value = null): ChoiceListInterface
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
    }

    public function loadChoicesForValues(
        array $values,
        callable $value = null
    ): array {
        $values = array_filter($values);

        if (null === $value) {
            return $values;
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(
        array $choices,
        callable $value = null
    ): array {
        $choices = array_filter($choices);

        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('application.state.new', self::TRANSLATION_DOMAIN))->setDesc('New'),
            (new Message('application.state.accept', self::TRANSLATION_DOMAIN))->setDesc('Accept'),
            (new Message('application.state.on_hold', self::TRANSLATION_DOMAIN))->setDesc('On Hold'),
            (new Message('application.state.reject', self::TRANSLATION_DOMAIN))->setDesc('Reject'),
        ];
    }
}
