<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Security\Limitation\Mapper;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @phpstan-property \Psr\Log\LoggerInterface $logger
 */
final class TaxonomyLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface, LoggerAwareInterface, TranslationContainerInterface
{
    use LoggerAwareTrait;

    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration,
        ?LoggerInterface $logger = null
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string, string>
     */
    protected function getSelectionChoices(): array
    {
        $taxonomyChoices = [];
        $taxonomies = $this->taxonomyConfiguration->getTaxonomies();

        foreach ($taxonomies as $taxonomyName) {
            $taxonomyChoices[$taxonomyName] = $taxonomyName;
        }

        return $taxonomyChoices;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getChoiceFieldOptions(): array
    {
        return [
            'choice_label' => static fn ($choice) => sprintf('taxonomy.%s', $choice),
            'choice_translation_domain' => 'ibexa_taxonomy',
        ];
    }

    /**
     * @return string[]
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        $taxonomies = $this->taxonomyConfiguration->getTaxonomies();
        $values = [];
        foreach ($limitation->limitationValues as $taxonomyName) {
            if (!in_array($taxonomyName, $taxonomies, true)) {
                $this->logger->error(sprintf(
                    'Could not map the Limitation value: could not find a Taxonomy with identifier %s',
                    $taxonomyName
                ));

                continue;
            }

            $values[] = $taxonomyName;
        }

        return $values;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                LimitationIdentifierToLabelConverter::convert('taxonomy'),
                'ibexa_content_forms_policies'
            )->setDesc('Taxonomy'),
        ];
    }
}
