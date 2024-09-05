<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\ChoiceType;

use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use IteratorAggregate;
use Symfony\Component\Form\ChoiceList\Loader\AbstractChoiceLoader;
use Traversable;

/**
 * @internal
 *
 * @phpstan-implements \IteratorAggregate<string, string>
 */
final class CustomerSiteChoiceLoader extends AbstractChoiceLoader implements IteratorAggregate
{
    private SecurityServiceInterface $securityService;

    public function __construct(SecurityServiceInterface $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @return iterable<string, string>
     */
    protected function loadChoices(): iterable
    {
        return $this->getIterator();
    }

    /**
     * @return \Traversable<string, string>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->securityService->getGrantedAccessList() as $customerId => $siteName) {
            yield (string)$siteName => (string)$customerId;
        }
    }
}
