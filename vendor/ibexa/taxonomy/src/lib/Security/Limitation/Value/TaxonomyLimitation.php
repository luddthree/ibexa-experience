<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Security\Limitation\Value;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class TaxonomyLimitation extends Limitation implements TranslationContainerInterface
{
    public const IDENTIFIER = 'Taxonomy';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message('policy.limitation.identifier.taxonomy', 'ibexa_content_forms_policies'))->setDesc('Taxonomy'),
        ];
    }
}
