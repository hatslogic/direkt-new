<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainCollection;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Storefront\Theme\AbstractThemePathBuilder;
use Shopware\Storefront\Theme\ThemeCompiler;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use HatslogicPageOptimisation\Exception\BaseUrlNotFoundException;
use HatslogicPageOptimisation\Exception\InvalidNpmVersionException;
use HatslogicPageOptimisation\Exception\InvalidNodeVersionException;
use HatslogicPageOptimisation\HatslogicPageOptimisation;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use HatslogicPageOptimisation\Service\NodeFinder;

/**
 * This service extracts critical CSS from CMS pages and outputs it in files for later use
 */
class CriticalCssGenerationService
{
    //node general path
    private string $nodeGeneralPath = '';
    private string $npmGeneralPath = '';
    public function __construct(
        private EntityRepository $salesChannelRepository,
        private EntityRepository $cmsPageRepository,
        private EntityRepository $productRepository,
        private Connection $connection,
        private AbstractSalesChannelContextFactory $salesChannelContextFactory,
        private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
        private ConfigService $configService,
        private AbstractThemePathBuilder $themePathBuilder,
        private NodeFinder $nodeFinder
    ) {}

    /**
     * @param Context              $context
     * @param OutputInterface|null $output
     *
     * @return array|null
     */
    public function generate(Context $context, ?OutputInterface $output = null): ?array
    {
        # Get all Sales Channels of type "Storefront" since these are the only ones that will have CSS output
        $salesChannels = $this->getStorefrontSalesChannels($context);

        # Get all CMS pages connect to at least one product OR category
        $cmsPages = $this->getCmsPages($context);

        # The directory near to the penthouse.js script and output directory of the critical CSS files for later use
        $nodeModulesRoot = $this->getPenthouseScriptDir();
        $outputDirBase = $this->getOutputDir();

        $this->output($output, "Output directory: $outputDirBase", OutputInterface::VERBOSITY_VERBOSE);

        $this->output($output, 'Validating Node and NPM versions...', OutputInterface::VERBOSITY_VERBOSE);
        $this->validateNodeNpmVersions();
        
        $nodePath = $this->nodeGeneralPath;
        $npmPath = $this->npmGeneralPath;
        
        if (!file_exists($nodeModulesRoot . '/node_modules')) {
            $nodeDir = dirname($nodePath);
            $npmDir = dirname($npmPath);
            $this->output($output, 'Installing required dependencies, do not terminate this script until completed...');
            
            // Construct the command using the resolved paths
            $command = "PATH=$nodeDir:\$PATH NODE_PATH=$nodeDir/lib/node_modules $npmDir/npm install --prefix $nodeModulesRoot";
        
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(300); // Set a timeout if needed
        
            try {
                $process->mustRun();
                $this->output($output, 'Required dependencies installed!');
            } catch (ProcessFailedException $exception) {
                // Output the error and rethrow the exception
                $errorMessage = 'Node module installation failed: ' . $exception->getMessage();
                $this->output($output, $errorMessage);
                throw new \RuntimeException($errorMessage, $exception->getCode(), $exception);
            }
        }
        $urlsFailed = [];
        $urlsSuccess = [];

        /** @var SalesChannelEntity $salesChannel */
        foreach ($salesChannels as $salesChannel) {
            
            if ($salesChannel->isMaintenance()) {
                $this->output($output, "Sales Channel {$salesChannel->getName()} in maintenance mode. Skipping.", OutputInterface::VERBOSITY_VERBOSE);
                continue;
            }

            # We need a SalesChannelContext to generate URL's for each CMS "Example" Page.
            $salesChannelContext = $this->getSalesChannelContext($salesChannel->getId());

            # Base URL to be used in SEO url generation
            $baseUrl = $this->getSalesChannelBaseUrl($salesChannelContext);
             if (!$baseUrl) {
                 continue;
             }
            # Path to the all.css file of the theme connected to the current Sales Channel
            $css = $this->getMainCssPath($salesChannel->getId());

            if (empty($css)) {
                $this->output($output, "No CSS file found for Sales Channel: {$salesChannel->getName()}. Skipping Sales Channel...", OutputInterface::VERBOSITY_VERBOSE, true);
                continue;
            }

            $this->output($output, "CSS file used: $css", OutputInterface::VERBOSITY_VERBOSE);

            # We get all config values in an ordered space seperated string for use in the penthouse.js command
            $config = $this->configService->getAll($salesChannel->getId())->__toString();

            $mapping = $this->configService->cmsPageCategoryMapping($salesChannel->getId());

            $this->output($output, "Config used: $config", OutputInterface::VERBOSITY_VERBOSE);

            /** @var CmsPageEntity $cmsPage */
            foreach ($cmsPages->getEntities() as $cmsPage) {
                $rawUrl = $this->getUrlForCmsPage($context, $cmsPage, $mapping);

                if ($rawUrl === null) {
                    continue;
                }

                # The ugly rawUrl might redirect to a pretty url which break the extraction process,
                # so we need to replace it with directly available SEO url
                $url = $this->seoUrlReplacer->replace($rawUrl, $baseUrl, $salesChannelContext);

                # The output path for the extracted critical CSS to be later read by the CriticalCssProcessor
                $outputPath = $outputDirBase . '/' . $salesChannel->getId() . $cmsPage->getId() . '.css';
                $this->output($output, "Starting extraction of critical CSS to file $outputPath", OutputInterface::VERBOSITY_VERBOSE);
                $this->output($output, "Crawling URL: $url", OutputInterface::VERBOSITY_VERBOSE);

                exec("touch $outputPath");

                $results = [];
                # Make sure node is executed in the directory of the node_modules, $result captures any console.log output.
				//dd("cd $nodeModulesRoot; /opt/plesk/node/18/bin/node bin/penthouse.js $url $css $outputPath $config");
                // dd("cd $nodeModulesRoot; $nodePath bin/penthouse.js $url $css $outputPath $config");
                $result = exec(
                    "cd $nodeModulesRoot; $nodePath bin/penthouse.js $url $css $outputPath $config",
                    $results
                );
				
                # If we get a result, something has gone wrong, might be a missing dependency or another failure with the script.
                if (!empty($result) || in_array('Failed to launch the browser process!', $results)) {
                    $this->output(
                        $output,
                        "Unexpected result received. Be sure all server requirements are met. Check the README.md for more information. RESULT: $result",
                        OutputInterface::VERBOSITY_QUIET,
                        true
                    );
                    $urlsFailed[] = $url;
                } else {
                    $urlsSuccess[] = $url;
                }
            }
        }

        return [
            'failed' => $urlsFailed,
            'success' => $urlsSuccess,
        ];
    }

    /**
     * @param CmsPageEntity $cmsPage
     * @param array         $mapping
     *
     * @return string|null
     */
    protected function getUrlForCmsPage(Context $context, CmsPageEntity $cmsPage, array $mapping = []): ?string
    {
        if (key_exists($cmsPage->getId(), $mapping)) {
            $category = $cmsPage->getCategories()->get($mapping[$cmsPage->getId()]);

            if ($category instanceof CategoryEntity) {
                return $this->seoUrlReplacer->generate('frontend.navigation.page', [
                    'navigationId' => $category->getId(),
                ]);
            }
        }

        foreach ($cmsPage->getCategories() as $category) {
            if (!$category->getActive()) {
                continue;
            }

            return $this->seoUrlReplacer->generate('frontend.navigation.page', [
                'navigationId' => $category->getId(),
            ]);
        }

        if ($product = $cmsPage->getProducts()->first()) {
            return $this->seoUrlReplacer->generate('frontend.detail.page', [
                'productId' => $product->getId(),
            ]);
        }
        $defaultCmsPageId = Defaults::CMS_PRODUCT_DETAIL_PAGE;
        if ($cmsPage->getId() == $defaultCmsPageId) {
            $productId = $this->getFirstActiveProductWithNullCmsPageId($context);
            if ($productId) {
                return $this->seoUrlReplacer->generate('frontend.detail.page', [
                    'productId' => $productId,
                ]);
            }
        }

        return null;
    }

    protected function getFirstActiveProductWithNullCmsPageId(Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('cmsPageId', null));
        $criteria->addFilter(new EqualsFilter('active', true)); 
        $criteria->setLimit(1); 

        $result = $this->productRepository->search($criteria, $context);
        $product = $result->first();

        if ($product instanceof ProductEntity) {
            return $product->getId();
        }

        return null;
    }


    /**
     * @param string $salesChannelId
     *
     * @return string
     * @throws Throwable
     */
    protected function getMainCssPath(string $salesChannelId): string
    {
        $result = $this->connection->fetchAssociative('SELECT HEX(`theme_id`) as theme_id FROM `theme_sales_channel` where sales_channel_id = :id', [
            'id' => Uuid::fromHexToBytes($salesChannelId),
        ]);

        if (!$result) {
            return "";
        }

        $themeId = strtolower($result['theme_id']);
        $themePrefix = $this->themePathBuilder->assemblePath($salesChannelId, $themeId);

        return $this->getProjectDir() . '/public/theme/' . $themePrefix . '/css/all.css';
    }

    /**
     * @return string
     */
    protected function getProjectDir(): string
    {
		 $currentDir = __DIR__;
			while (!file_exists($currentDir . '/vendor/shopware')) {
				// Move one level up
				$parentDir = dirname($currentDir);

				// If we've reached the top-level directory, break to avoid infinite loop
				if ($parentDir === $currentDir) {
					throw new Exception("Project root directory not found (composer.json is missing).");
				}

				$currentDir = $parentDir;
			}

		return $currentDir;
    }

    /**
     * @param Context $context
     *
     * @return EntitySearchResult
     */
    protected function getStorefrontSalesChannels(Context $context): EntitySearchResult
    {
        $criteria = (new Criteria())->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        $criteria->addAssociation('domains');

        return $this->salesChannelRepository->search($criteria, $context);
    }

    /**
     * @param Context $context
     *
     * @return EntitySearchResult
     */
    protected function getCmsPages(Context $context): EntitySearchResult
    {
        $defaultCmsPageId = Defaults::CMS_PRODUCT_DETAIL_PAGE;

        $criteria = new Criteria();
        $criteria->addAssociations(['products', 'categories']);
        // Add filter to include CMS pages that either have a product/category or match the default CMS page ID
        $criteria->addFilter(new OrFilter([
            new NotFilter(MultiFilter::CONNECTION_AND, [
                new EqualsFilter('products.id', null),
                new EqualsFilter('categories.id', null),
            ]),
            new EqualsFilter('id', $defaultCmsPageId) // Include default CMS page ID as a fallback
        ]));

        return $this->cmsPageRepository->search($criteria, $context);
    }

    /**
     * @param SalesChannelContext $salesChannelContext
     *
     * @return string
     */
    protected function getSalesChannelBaseUrl(SalesChannelContext $salesChannelContext)
    {
        $salesChannel = $salesChannelContext->getSalesChannel();
        $domains = $salesChannelContext->getSalesChannel()->getDomains();
        $languageId = $salesChannelContext->getSalesChannel()->getLanguageId();

        if (!$domains instanceof SalesChannelDomainCollection) {
            return false;
        }

        /** @var SalesChannelDomainEntity $domain */
        foreach ($domains as $domain) {
            if ($domain->getLanguageId() === $languageId) {
                return $domain->getUrl();
            }
        }

        return false;
    }

    private function getSalesChannelContext(string $salesChannelId): SalesChannelContext
    {
        return $this->salesChannelContextFactory->create(Uuid::randomHex(), $salesChannelId);
    }

    private function getPenthouseScriptDir(): string
    {
        return HatslogicPageOptimisation::getPluginDir() . '/Resources/app/storefront/src';
    }

    private function getOutputDir(): string
    {
        return HatslogicPageOptimisation::getPluginDir() . '/Resources/app/storefront/src/css/critical';
    }

    private function output(?OutputInterface $output, string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL, bool $error = false): void
    {
        if ($output === null || $output->getVerbosity() < $verbosity) {
            if ($error) {
                throw new \Exception($message);
            }

            return;
        }

        if (!$error) {
            $output->writeln("<comment>$message</comment>");
        } else {
            $output->writeln("<error>$message</error>");
        }
    }

    private function validateNodeNpmVersions(): void
    {
        $npmVersion = '';
        $nodeVersion = '';

        // NPM Version Detection Strategies
        $npmVersionStrategies = [
            // Strategy 1: Use configured NPM path
            function() {
                $npmPathConfig = $this->configService->npmPath();
                if ($npmPathConfig) {
                    $npmNodePath = dirname(dirname($npmPathConfig));
                    $npmPath = $npmNodePath . '/bin/npm';
                    $command = "PATH=$npmNodePath/bin:\$PATH NODE_PATH=$npmNodePath/lib/node_modules $npmPath -v";
                    return [$command, $npmPath];
                }
                return null;
            },
            // Strategy 2: Global node
            function() {
                exec("which node", $npmResolvedPath);
                if (!empty($npmResolvedPath)) {
                    $npmPath = trim($npmResolvedPath[0]);
                    return [$npmPath." -v", $npmPath];
                }
                
                return null;
            },
            // Strategy 3: Plesk default path
            function() {
                $path = "/opt/plesk/node/18/bin/npm";
                return ["/opt/plesk/node/18/bin/npm -v", $path];
            },
        ];
        // Node Version Detection Strategies
        $nodeVersionStrategies = [
            // Strategy 1: Use configured Node path
            function() {
                $nodePathConfig = $this->configService->nodePath();
                if ($nodePathConfig) {
                    $nodeNodePath = dirname(dirname($nodePathConfig));
                    $nodePath = $nodeNodePath . '/bin/node';
                    $command = "PATH=$nodeNodePath/bin:\$PATH NODE_PATH=$nodeNodePath/lib/node_modules $nodePath -v";
                    return [$command, $nodePath];
                }
                return null;
            },
            // Strategy 2: Global node
            function() {
                exec("which node", $nodeResolvedPath);
                if (!empty($nodeResolvedPath)) {
                    $nodePath = trim($nodeResolvedPath[0]);
                    return [$nodePath." -v", $nodePath];
                }
                
                return null;
            }
        ];

        // Detect NPM Version and Set General Path
        [$npmVersion, $this->npmGeneralPath] = $this->detectVersionWithPath(
            $npmVersionStrategies, 
            new InvalidNpmVersionException("Unknown", '>=6.14.15')
        );

        // Detect Node Version and Set General Path
        [$nodeVersion, $this->nodeGeneralPath] = $this->detectVersionWithPath(
            $nodeVersionStrategies, 
            new InvalidNodeVersionException("Unknown", '>=12.22.7')
        );

        // Validate NPM Version
        $this->validateVersion($npmVersion, 6, '>=6.14.15', InvalidNpmVersionException::class);

        // Validate Node Version
        $this->validateVersion($nodeVersion, 12, '>=12.22.7', InvalidNodeVersionException::class);
    }

    private function detectVersionWithPath(array $strategies, \Exception $fallbackException): array
    {
        foreach ($strategies as $strategy) {
            $result = $strategy();
            if (!$result) {
                continue;
            }

            [$command, $path] = $result;

            try {
                $process = Process::fromShellCommandline($command);
                $process->setTimeout(300);
                $process->mustRun();
                $version = trim($process->getOutput());

                if (!empty($version)) {
                    // Remove 'v' prefix if present
                    $version = str_replace('v', '', $version);
                    return [$version, $path];
                }
            } catch (ProcessFailedException $exception) {
                // Continue to next strategy
                continue;
            }
        }

        // If all strategies fail, throw the fallback exception
        throw $fallbackException;
    }

    private function validateVersion(string $version, int $minMajorVersion, string $expectedVersionRange, string $exceptionClass)
    {
        $versionParts = explode('.', $version);
        $majorVersion = intval($versionParts[0]);

        if ($majorVersion < $minMajorVersion) {
            /** @var InvalidNodeVersionException|InvalidNpmVersionException $exception */
            $exception = new $exceptionClass($version, $expectedVersionRange);
            throw $exception;
        }
    }
}
