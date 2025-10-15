<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Struct;

class CriticalAssetsConfigStruct
{
    protected int $viewportWidth;
    protected int $viewportHeight;
    protected string $forceInclude;
    protected string $forceExclude;
    protected int $generationTimeout;
    protected int $renderWaitTime;
    protected bool $keepLargerMediaQueries;
    protected bool $enableJSRequests;

    public function __toString(): string
    {
        # The order of these values matter! Used in src/Resources/app/storefront/src/bin/penthouse.js
        $values = [
            $this->getViewportWidth(),
            $this->getViewportHeight(),
            !empty($this->getForceInclude()) ? $this->getForceInclude() : "[]",
            !empty($this->getForceExclude()) ? $this->getForceExclude() : "[]",
            $this->getGenerationTimeout(),
            $this->getRenderWaitTime(),
            $this->isKeepLargerMediaQueries() ? "true" : "false",
            $this->isEnableJSRequests() ? "true" : "false",
        ];

        return implode(' ', $values);
    }

    /**
     * @return int
     */
    public function getViewportWidth(): int
    {
        return $this->viewportWidth;
    }

    /**
     * @param int $viewportWidth
     */
    public function setViewportWidth(int $viewportWidth): void
    {
        $this->viewportWidth = $viewportWidth;
    }

    /**
     * @return int
     */
    public function getViewportHeight(): int
    {
        return $this->viewportHeight;
    }

    /**
     * @param int $viewportHeight
     */
    public function setViewportHeight(int $viewportHeight): void
    {
        $this->viewportHeight = $viewportHeight;
    }

    /**
     * @return string
     */
    public function getForceInclude(): string
    {
        return $this->forceInclude;
    }

    /**
     * @param string $forceInclude
     */
    public function setForceInclude(string $forceInclude): void
    {
        $this->forceInclude = $forceInclude;
    }

    /**
     * @return string
     */
    public function getForceExclude(): string
    {
        return $this->forceExclude;
    }

    /**
     * @param string $forceExclude
     */
    public function setForceExclude(string $forceExclude): void
    {
        $this->forceExclude = $forceExclude;
    }

    /**
     * @return int
     */
    public function getGenerationTimeout(): int
    {
        return $this->generationTimeout;
    }

    /**
     * @param int $generationTimeout
     */
    public function setGenerationTimeout(int $generationTimeout): void
    {
        $this->generationTimeout = $generationTimeout;
    }

    /**
     * @return int
     */
    public function getRenderWaitTime(): int
    {
        return $this->renderWaitTime;
    }

    /**
     * @param int $renderWaitTime
     */
    public function setRenderWaitTime(int $renderWaitTime): void
    {
        $this->renderWaitTime = $renderWaitTime;
    }

    /**
     * @return bool
     */
    public function isKeepLargerMediaQueries(): bool
    {
        return $this->keepLargerMediaQueries;
    }

    /**
     * @param bool $keepLargerMediaQueries
     */
    public function setKeepLargerMediaQueries(bool $keepLargerMediaQueries): void
    {
        $this->keepLargerMediaQueries = $keepLargerMediaQueries;
    }

    /**
     * @return bool
     */
    public function isEnableJSRequests(): bool
    {
        return $this->enableJSRequests;
    }

    /**
     * @param bool $enableJSRequests
     */
    public function setEnableJSRequests(bool $enableJSRequests): void
    {
        $this->enableJSRequests = $enableJSRequests;
    }
}
