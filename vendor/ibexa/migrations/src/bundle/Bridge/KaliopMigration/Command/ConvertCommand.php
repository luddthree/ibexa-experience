<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Command;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

final class ConvertCommand extends Command
{
    protected static $defaultName = 'ibexa:migrations:kaliop:convert';

    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    private FilesystemOperator $filesystem;

    public function __construct(
        SerializerInterface $serializer,
        FilesystemOperator $filesystem
    ) {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('input', null, InputOption::VALUE_REQUIRED, 'File to convert from')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'File to output to')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFilename = $input->getOption('input');
        Assert::notNull($inputFilename, '"--input" option is required');
        Assert::string($inputFilename, '"--input" option is required');

        $outputFilename = $input->getOption('output');
        Assert::notNull($outputFilename, '"--output" option is required');
        Assert::string($outputFilename, '"--output" option is required');

        Assert::true($this->filesystem->fileExists($inputFilename));
        $content = $this->filesystem->read($inputFilename);

        $steps = $this->serializer->deserialize($content, StepInterface::class . '[]', 'yaml', [
            'output' => $outputFilename,
        ]);

        Assert::isIterable($steps);
        $serialized = $this->serializer->serialize($steps, 'yaml');
        $this->filesystem->write($outputFilename, $serialized);

        return 0;
    }
}

class_alias(ConvertCommand::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Command\ConvertCommand');
