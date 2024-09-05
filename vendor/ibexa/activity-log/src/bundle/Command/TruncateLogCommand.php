<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Command;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TruncateLogCommand extends Command
{
    protected static $defaultName = 'ibexa:activity-log:truncate';

    protected static $defaultDescription = 'Removes entries from activity log storage that are scheduled for removal';

    private ActivityLogServiceInterface $activityLogService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService
    ) {
        $this->activityLogService = $activityLogService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->activityLogService->truncate();

        $io->success('Log has been successfully truncated.');

        return self::SUCCESS;
    }
}
