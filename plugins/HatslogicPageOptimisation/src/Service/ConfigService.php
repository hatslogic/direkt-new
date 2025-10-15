<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use HatslogicPageOptimisation\Struct\CriticalAssetsConfigStruct;

class ConfigService {

    protected SystemConfigService $configService;

    /**
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        SystemConfigService $systemConfigService
    )
    {
        $this->configService = $systemConfigService;
    }

    public function getAll(string $salesChannelId = null): CriticalAssetsConfigStruct
    {
        $configStruct = new CriticalAssetsConfigStruct();
        $configStruct->setViewportWidth($this->viewportWidth($salesChannelId));
        $configStruct->setViewportHeight($this->viewportHeight($salesChannelId));
        $configStruct->setForceInclude($this->forceInclude($salesChannelId));
        $configStruct->setForceExclude($this->forceExclude($salesChannelId));
        $configStruct->setGenerationTimeout($this->generationTimeout($salesChannelId));
        $configStruct->setRenderWaitTime($this->renderWaitTime($salesChannelId));
        $configStruct->setKeepLargerMediaQueries($this->keepLargerMediaQueries($salesChannelId));
        $configStruct->setEnableJSRequests($this->enableJSRequests($salesChannelId));

        return $configStruct;
    }

    public function viewportWidth(string $salesChannelId = null): int
    {
        return $this->configService->getInt('HatslogicPageOptimisation.config.viewportWidth', $salesChannelId);
    }

    public function npmPath(string $salesChannelId = null): string
    {
        return $this->configService->getString('HatslogicPageOptimisation.config.npmPath', $salesChannelId);
    }

    public function nodePath(string $salesChannelId = null): string
    {
        return $this->configService->getString('HatslogicPageOptimisation.config.nodePath', $salesChannelId);
    }

    public function viewportHeight(string $salesChannelId = null): int
    {
        return $this->configService->getInt('HatslogicPageOptimisation.config.viewportHeight', $salesChannelId);
    }

    public function forceInclude(string $salesChannelId = null): string
    {
        $cssToInclude = $this->configService->getString('HatslogicPageOptimisation.config.forceInclude', $salesChannelId);

        $cssToInclude = explode("\n", rtrim($cssToInclude, ' ,'));
        foreach ($cssToInclude as $index => &$item) {
            if (empty($item)) {
                unset($cssToInclude[$index]); continue;
            }

            $cssToInclude[$index] = '"'.trim($item, ' ').'"';
        }

        $cssToInclude = json_encode($cssToInclude);

        return $cssToInclude ?: "";
    }

    public function forceExclude(string $salesChannelId = null): string
    {
        $cssToExclude = $this->configService->getString('HatslogicPageOptimisation.config.forceExclude', $salesChannelId);

        $cssToExclude = explode("\n", rtrim($cssToExclude, ' ,'));
        foreach ($cssToExclude as $index => &$item) {
            if (empty($item)) {
                unset($cssToExclude[$index]); continue;
            }

            $cssToExclude[$index] = '"'.trim($item, ' ').'"';
        }

        $cssToExclude = json_encode($cssToExclude);

        return $cssToExclude ?: "";
    }

    public function generationTimeout(string $salesChannelId = null): int
    {
        return $this->configService->getInt('HatslogicPageOptimisation.config.generationTimeout', $salesChannelId);
    }

    public function renderWaitTime(string $salesChannelId = null): int
    {
        return $this->configService->getInt('HatslogicPageOptimisation.config.renderWaitTime', $salesChannelId);
    }

    public function keepLargerMediaQueries(string $salesChannelId = null): bool
    {
        return $this->configService->getBool('HatslogicPageOptimisation.config.keepLargerMediaQueries', $salesChannelId);
    }

    public function enableJSRequests(string $salesChannelId = null): bool
    {
        return $this->configService->getBool('HatslogicPageOptimisation.config.enableJSRequests', $salesChannelId);
    }

    public function additionalCriticalCss(string $salesChannelId = null): string
    {
        return $this->configService->getString('HatslogicPageOptimisation.config.additionalCriticalCss', $salesChannelId);
    }

    public function cmsPageCategoryMapping(string $salesChannelId = null): array
    {
        $mapping = $this->configService->getString('HatslogicPageOptimisation.config.cmsPageCategoryMapping', $salesChannelId);

        $mapping = json_decode($mapping, true);

        if ($mapping === false || $mapping === null) {
            return [];
        }

        return $mapping;
    }

    public function getCustomPreloadLinks(string $salesChannelId = null)
    {
        $links = (string) $this->configService->get('HatslogicPageOptimisation.config.PreloadLinks', $salesChannelId);
        $links = explode("\n", $links);
        foreach ($links as &$link) {
            $link = trim($link, " \r");
        }
        $links = array_unique($links);
        $links = array_filter($links);
        return $links;
    }

    public function isPreloadEnable(string $salesChannelId = null): bool
    {
        return $this->configService->getBool('HatslogicPageOptimisation.config.EnablePrelinkCode', $salesChannelId);
    }

    public function isCriticalCssScheduleTaskEnable(string $salesChannelId = null): bool
    {
        return $this->configService->getBool('HatslogicPageOptimisation.config.EnableCriticalCssScheduleTask', $salesChannelId);
    }
}
