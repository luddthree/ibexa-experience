<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Command;

use function array_key_exists;
use function array_values;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Migration\Dumper\MigrationDumperInterface;
use Ibexa\Migration\Dumper\MigrationFile;
use Ibexa\Migration\Generator\Exception\InvalidModeException;
use Ibexa\Migration\Generator\Exception\InvalidTypeException;
use Ibexa\Migration\Generator\MigrationGeneratorsManager;
use Ibexa\Migration\Generator\Mode;
use function implode;
use InvalidArgumentException;
use function is_numeric;
use function is_string;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class GenerateCommand extends Command
{
    protected static $defaultName = 'ibexa:migrations:generate';

    /** @var \Ibexa\Migration\Generator\MigrationGeneratorsManager */
    private $generatorsManager;

    /** @var \Ibexa\Migration\Dumper\MigrationDumperInterface */
    private $dumper;

    /** @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface */
    private $normalizer;

    /** @var \Symfony\Component\Serializer\Encoder\EncoderInterface */
    private $encoder;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var string */
    private $defaultUserLogin;

    public function __construct(
        MigrationGeneratorsManager $generatorsManager,
        MigrationDumperInterface $dumper,
        NormalizerInterface $normalizer,
        EncoderInterface $encoder,
        UserService $userService,
        PermissionResolver $permissionResolver,
        string $defaultUserLogin
    ) {
        $this->generatorsManager = $generatorsManager;
        $this->dumper = $dumper;
        $this->normalizer = $normalizer;
        $this->encoder = $encoder;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->defaultUserLogin = $defaultUserLogin;

        parent::__construct();
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null === $input->getOption('type')) {
            $helper = $this->getHelper('question');

            $supportedTypes = $this->generatorsManager->getSupportedTypes();
            $question = new ChoiceQuestion('Please select type', $supportedTypes);
            $question->setValidator(static function ($answer) use ($supportedTypes): string {
                if (is_string($answer) && array_key_exists($answer, $supportedTypes)) {
                    return $answer;
                }

                if (is_numeric($answer)) {
                    $supportedTypes = array_values($supportedTypes);
                    if (array_key_exists((int) $answer, $supportedTypes)) {
                        return $supportedTypes[$answer];
                    }
                }

                throw new InvalidArgumentException('You need to provide a supported type');
            });
            $question->setMaxAttempts(3);

            $type = $helper->ask($input, $output, $question);
            $input->setOption('type', $type);
        }

        if (null === $input->getOption('mode')) {
            $helper = $this->getHelper('question');

            $selectedType = $input->getOption('type');
            $supportedModes = $this->generatorsManager->getSupportedModes($selectedType);
            $question = new ChoiceQuestion('Please select mode', $supportedModes);
            $question->setValidator(static function ($answer) use ($supportedModes): string {
                if (is_string($answer) && array_key_exists($answer, $supportedModes)) {
                    return $answer;
                }

                if (is_numeric($answer)) {
                    $supportedModes = array_values($supportedModes);
                    if (array_key_exists((int) $answer, $supportedModes)) {
                        return $supportedModes[$answer];
                    }
                }

                throw new InvalidArgumentException('You need to provide a supported mode');
            });
            $question->setMaxAttempts(3);

            $mode = $helper->ask($input, $output, $question);
            $input->setOption('mode', $mode);
        }

        if (null === $input->getOption('format')) {
            $helper = $this->getHelper('question');

            $question = new Question('Please specify format');
            $question->setValidator(function ($answer): string {
                if (!is_string($answer) || !$this->encoder->supportsEncoding($answer)) {
                    throw new InvalidArgumentException('You need to provide a supported format');
                }

                return $answer;
            });
            $question->setMaxAttempts(3);

            $format = $helper->ask($input, $output, $question);
            $input->setOption('format', $format);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Generate migration file.')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Type of migration')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'Mode of migration')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Format of migration', 'yaml')
            ->addOption('match-property', null, InputOption::VALUE_REQUIRED, 'Property name to perform matching')
            ->addOption('value', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Property value to perform matching')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'file')
            ->addOption('user-context', null, InputOption::VALUE_REQUIRED, 'User identifier used when performing generation')
            ->setHelp('This command allows you to generate migration file')
            ->addUsage('--type=["' . implode('"|"', $this->generatorsManager->getSupportedTypes()) . '"] ')
            ->addUsage('--mode=["' . implode('"|"', $this->generatorsManager->getSupportedModes()) . '"] ')
            ->addUsage('--format=yaml')
            ->addUsage('--match-property=property-name')
            ->addUsage('--value=XXX --value=YYY')
            ->addUsage('--file=migration-file-name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $this->getType($input);
        $mode = $this->getMode($input, $type);
        $format = $this->getFormat($input);
        $values = $input->getOption('value');
        $matchProperty = $input->getOption('match-property');

        if (null !== $matchProperty) {
            Assert::notEmpty($values, '"--value" option is required when using "--match-property"');
        }

        $context = [
            'format' => $format,
            'value' => $values ?: ['*'],
            'match-property' => $matchProperty,
        ];

        $this->handleUserContext($input);

        $migrationFile = $this->getMigrationFile($input);
        $data = $this->generatorsManager->generate($type, $mode, $context);

        $this->dumper->dump(
            $this->getEncodedData($data, $format, $context),
            $migrationFile
        );

        $output->writeln([
            'Done!',
            'Generated migration file: ' . $migrationFile->getFilename(),
        ]);

        return 0;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\StepInterface[] $data
     * @param array<mixed> $context
     *
     * @return string[]
     */
    private function getEncodedData(iterable $data, string $format, array $context): iterable
    {
        foreach ($data as $dataChunk) {
            $normalized = $this->normalizer->normalize($dataChunk, $format, $context);
            yield $this->encoder->encode([$normalized], $format, $context);
        }
    }

    private function getMigrationFile(InputInterface $input): MigrationFile
    {
        if ($input->getOption('file')) {
            return new MigrationFile($input->getOption('file'));
        }

        return MigrationFile::createWithGeneratedName(
            $input->getOption('type'),
            $input->getOption('format')
        );
    }

    private function getType(InputInterface $input): string
    {
        $type = $input->getOption('type');
        if (null === $type) {
            throw new InvalidArgumentException('"type" option is required.');
        }

        $supportedTypes = $this->generatorsManager->getSupportedTypes();
        if (false === array_key_exists($type, $supportedTypes)) {
            throw new InvalidTypeException($type, $supportedTypes);
        }

        return $type;
    }

    private function getMode(InputInterface $input, string $type): Mode
    {
        $mode = $input->getOption('mode');
        if (null === $mode) {
            throw new InvalidArgumentException('"mode" option is required.');
        }

        $supportedModes = $this->generatorsManager->getSupportedModes($type);
        if (false === array_key_exists($mode, $supportedModes)) {
            throw new InvalidModeException($mode, $supportedModes, $type);
        }

        return new Mode($mode);
    }

    private function getFormat(InputInterface $input): string
    {
        $format = $input->getOption('format');
        if (false === $this->encoder->supportsEncoding($format)) {
            throw new InvalidArgumentException(sprintf('"%s" format is not supported.', $format));
        }

        return $format;
    }

    private function handleUserContext(InputInterface $input): void
    {
        $userIdentifier = $input->getOption('user-context') ?: $this->defaultUserLogin;
        $user = $this->userService->loadUserByLogin($userIdentifier);
        $this->permissionResolver->setCurrentUserReference($user);
    }
}

class_alias(GenerateCommand::class, 'Ibexa\Platform\Bundle\Migration\Command\GenerateCommand');
