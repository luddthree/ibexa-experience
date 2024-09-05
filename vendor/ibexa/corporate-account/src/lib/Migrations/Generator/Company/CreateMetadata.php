<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Generator\Company;

use DateTime;
use Ibexa\Contracts\CorporateAccount\Values\Company;

final class CreateMetadata
{
    public ?int $creatorId;

    public ?DateTime $modificationDate;

    public ?DateTime $publicationDate;

    public ?string $remoteId;

    public function __construct(
        ?int $creatorId = null,
        ?DateTime $modificationDate = null,
        ?DateTime $publicationDate = null,
        ?string $remoteId = null
    ) {
        $this->creatorId = $creatorId;
        $this->modificationDate = $modificationDate;
        $this->publicationDate = $publicationDate;
        $this->remoteId = $remoteId;
    }

    public static function createFromApi(Company $company): self
    {
        $contentInfo = $company->getContent()->contentInfo;

        return new self(
            $contentInfo->ownerId,
            $contentInfo->modificationDate,
            $contentInfo->publishedDate,
            $contentInfo->remoteId
        );
    }
}
