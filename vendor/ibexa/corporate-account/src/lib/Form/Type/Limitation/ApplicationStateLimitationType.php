<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Limitation;

use Ibexa\AdminUi\Translation\Extractor\LimitationTranslationExtractor;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Values\Limitation\ApplicationStateLimitation;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class ApplicationStateLimitationType extends AbstractType implements TranslationContainerInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choiceOptions = [
            'multiple' => true,
            'expanded' => false,
            'required' => false,
            'label' => LimitationTranslationExtractor::identifierToLabel(
                ApplicationStateLimitation::IDENTIFIER
            ),
            'choices' => $this->getSelectionChoices(),
        ];

        $builder
            ->add('from', ChoiceType::class, $choiceOptions)
            ->add('to', ChoiceType::class, $choiceOptions);
    }

    /**
     * @return array<string, string>
     */
    private function getSelectionChoices(): array
    {
        $states = $this->configResolver->getParameter(
            'corporate_account.application.states'
        );

        return array_combine($states, $states);
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('policy.limitation.identifier.applicationstate', 'ibexa_content_forms_policies'))->setDesc('Application State'),
        ];
    }
}
