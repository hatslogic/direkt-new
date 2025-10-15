<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Processor;

use DOMDocument;
use DOMElement;
use Masterminds\HTML5;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheAdapterInterface;
use HatslogicPageOptimisation\Service\ConfigService;
use HatslogicPageOptimisation\HatslogicPageOptimisation;

class CriticalCssProcessor
{
    protected LoggerInterface $logger;
    protected ConfigService $configService;
    private CacheAdapterInterface $cache;

    /**
     * @param CacheAdapterInterface $cache
     * @param LoggerInterface       $logger
     * @param ConfigService         $configService
     */
    public function __construct(
        CacheAdapterInterface $cache,
        LoggerInterface $logger,
        ConfigService $configService
    ) {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->configService = $configService;
    }

    /**
     * @param string      $rawHtml
     * @param string      $name
     * @param string|null $salesChannelId
     *
     * @return string
     */
    public function process(string $rawHtml, string $name, string $salesChannelId = null): string
    {
        $cssCacheItem = $this->cache->getItem(
            $this->getCacheName($name)
        );

        if (!$cssCacheItem->isHit()) {
            try {
                # Get contents from generated critical CSS file
                $criticalCss = file_get_contents($this->getCriticalAssetFilePath($name));
            } catch (\Throwable $e) {
                return $rawHtml;
            }

            # If no critical CSS found we return the raw html
            if ($criticalCss === false) {
                return $rawHtml;
            }

            $criticalCss .= $this->configService->additionalCriticalCss($salesChannelId);
            
            # Safe to cache
            $cssCacheItem->set($criticalCss);
            $this->cache->save($cssCacheItem);
        } else {
            $criticalCss = $cssCacheItem->get() ?? false;
        }

        $linkTags = [];

        $html5 = new HTML5();
        $document = $html5->loadHTML($rawHtml);

        foreach ($document->getElementsByTagName('link') as $linkTag) {
            /** @var DOMElement $linkTag */
            if ($linkTag->getAttribute('rel') !== 'stylesheet') {
                continue;
            }

            $linkTag->setAttributeNode(new \DOMAttr('async'));

            $linkTags[] = $linkTag;
        }

        return $this->injectCriticalCssInDocument(
            $document,
            $criticalCss,
            $rawHtml,
            $linkTags
        );
    }

    /**
     * @param DOMDocument $document
     * @param string      $criticalCss
     * @param string      $rawHtml
     * @param array       $linkTags
     *
     * @return string
     */
    protected function injectCriticalCssInDocument(DOMDocument $document, string $criticalCss, string $rawHtml, array $linkTags): string
    {
        $html5 = new HTML5();

        try {
            /** @var DOMElement $bodyNode */
            foreach ($document->getElementsByTagName('body') as $bodyNode) {
                # move link tags to footer
                foreach ($linkTags as $linkTag) {
                    $bodyNode->appendChild($linkTag);
                }
                break;
            }

            $rawHtml = $html5->saveHTML($document);

            return str_replace('<!--criticalcss-->', "<style>$criticalCss</style>", $rawHtml);
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
            return $rawHtml;
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getCriticalAssetFilePath(string $name): string
    {
        return HatslogicPageOptimisation::getPluginDir() . '/Resources/app/storefront/src/css/critical/'.$name.'.css';
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getCacheName(string $name): string
    {
        return "hatslogic-critical-css-$name";
    }
}
