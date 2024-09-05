<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Fastly\Command;

use Ibexa\Bundle\Core\Imagine\IORepositoryResolver;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Fastly\ImageOptimizer\AliasConfigurationMapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @phpstan-import-type TAliasConfig from \Ibexa\Fastly\ImageOptimizer\AliasConfigurationMapper
 */
final class FastlyMigrateConfigurationCommand extends Command
{
    protected static $defaultName = 'ibexa:fastly:migrate-configuration';

    protected static $defaultDescription = 'Dumps draft Fastly IO YAML configuration migrated from built-in alias variations.';

    /** @var string[] */
    private array $siteAccessList;

    private ConfigResolverInterface $configResolver;

    private AliasConfigurationMapper $aliasConfigurationMapper;

    /**
     * @param array<string> $siteAccessList
     */
    public function __construct(
        array $siteAccessList,
        ConfigResolverInterface $configResolver,
        AliasConfigurationMapper $aliasConfigurationMapper
    ) {
        $this->siteAccessList = $siteAccessList;
        $this->configResolver = $configResolver;
        $this->aliasConfigurationMapper = $aliasConfigurationMapper;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $aliasConfigurations = [];
        foreach ($this->siteAccessList as $siteAccess) {
            if ($this->configResolver->hasParameter('image_variations', null, $siteAccess)) {
                $configuration = $this->configResolver->getParameter('image_variations', null, $siteAccess) ?? [];
                $aliasConfigurations[$siteAccess] = $configuration;
            } else {
                $aliasConfigurations[$siteAccess] = [];
            }
        }

        $common = $this->getCommonConfiguration($aliasConfigurations);
        $commonVariations = array_keys($common);

        $unsupported = [];
        $configuration = [];

        // Dump common variations into 'default' scope
        foreach ($common as $variation => $aliasConfiguration) {
            try {
                $configuration['ibexa']['system']['default']['fastly_variations'][$variation]
                    = $this->aliasConfigurationMapper->mapConfiguration($aliasConfiguration);
            } catch (InvalidArgumentException $exception) {
                $unsupported["ibexa.system.default.image_variations.$variation"] = $exception->getMessage();
            }
        }

        // Dump variations into site access scope
        foreach ($aliasConfigurations as $siteAccess => $aliasConfiguration) {
            foreach ($aliasConfiguration as $variation => $variationConfiguration) {
                try {
                    if (!in_array($variation, $commonVariations, true)
                        && $variation !== IORepositoryResolver::VARIATION_ORIGINAL) {
                        $configuration['ibexa']['system'][$siteAccess]['fastly_variations'][$variation]
                            = $this->aliasConfigurationMapper->mapConfiguration($variationConfiguration);
                    }
                } catch (InvalidArgumentException $exception) {
                    $unsupported["$siteAccess:$variation"] = $exception->getMessage();
                }
            }
        }

        if (!empty($configuration)) {
            $output->writeln('YAML configuration:');
            $output->writeln('');
            $output->writeln(sprintf('<info>%s</info>', Yaml::dump($configuration, 8)));
            $output->writeln('');
        }

        if (!empty($unsupported)) {
            $output->writeln('Unsupported variation configurations:');
            $output->writeln('');
            foreach ($unsupported as $key => $reason) {
                $output->writeln(sprintf('- <info>%s</info>: <error>%s</error>', $key, $reason));
            }
            $output->writeln('');
        }

        return self::SUCCESS;
    }

    /**
     * @phpstan-param array<TAliasConfig|null> $configurations
     *
     * @return array<TAliasConfig>
     */
    private function getCommonConfiguration(array $configurations): array
    {
        $parameters = [
            ...array_values($configurations),
            static function (?array $a, ?array $b): int {
                return $a <=> $b;
            },
        ];

        return array_filter(
            array_uintersect_assoc(...$parameters),
            static function (string $variation): bool {
                return $variation !== IORepositoryResolver::VARIATION_ORIGINAL;
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
