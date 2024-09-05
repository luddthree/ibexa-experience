<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Company;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Symfony\Contracts\EventDispatcher\Event;
use UnexpectedValueException;

final class BeforeCreateCompanyAddressBookFolderEvent extends Event
{
    private Company $company;

    private ?Content $content = null;

    public function __construct(
        Company $company
    ) {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getContent(): Content
    {
        if (!$this->hasContent()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasContent() or set it using setContent() before you call the getter.', Content::class));
        }

        return $this->content;
    }

    public function setContent(?Content $content): void
    {
        $this->content = $content;
    }

    public function hasContent(): bool
    {
        return $this->content instanceof Content;
    }
}
