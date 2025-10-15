<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Service;

use JSMin\JSMin;
use Shopware\Core\Framework\Adapter\Cache\CacheCompressor;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class MinifyService
{
    private const JAVASCRIPT_PLACEHOLDER = '##SCRIPTPOSITION##';
    private const SPACE_PLACEHOLDER = '##SPACE##';

    private ?Response $response = null;

    public function __construct(
        private readonly AdapterInterface $cache,
        private readonly SystemConfigService $systemConfigService
    ) {}

    public function setResponse(Response $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function minify(): void
    {
        $response = $this->response;

        if ($response->getContent() === false) {
            return;
        }

        $content = $response->getContent();
        $shouldAddCompressionHeader = $this->systemConfigService->getBool('HatslogicPageOptimisation.config.addCompressionHeader');
        
        $startTime = $shouldAddCompressionHeader ? microtime(true) : 0;
        $lengthInitialContent = strlen($content);

        if ($lengthInitialContent === 0) {
            return;
        }

        // Process the content
        $this->minifySourceTypes($content);
        $inlineScripts = $this->extractCombinedInlineScripts($content);
        $this->minifyHtml($content);

        // Add the minified javascript back into the content
        $content = str_replace(self::JAVASCRIPT_PLACEHOLDER, "<script>{$inlineScripts}</script>", $content);

        // Optionally add compression header
        if ($shouldAddCompressionHeader) {
            $this->addCompressionHeader($response->headers, $content, $lengthInitialContent, $startTime);
        }

        $response->setContent($content);
    }

    private function minifyJavascript(string $jsContent): string
    {
        return (new JSMin($jsContent))->min();
    }

    private function minifyHtml(string &$content): void
    {
        $search = [
            '/(\n|^)(\x20+|\t)/',
            '/(\n|^)\/\/(.*?)(\n|$)/',
            '/\n/',
            '/\<\!--.*?-->/',
            '/(\x20+|\t)/',
            '/\s+\<label/',
            '/span\>\s+/',
            '/\s+\<span/',
            '/button\>\s+/',
            '/\s+\<button/',
            '/\>\s+\</',
            '/(\"|\')\s+\>/', 
            '/=\s+(\"|\')/', 
            '/' . self::SPACE_PLACEHOLDER . '/',
        ];

        $replace = [
            "\n",
            "\n",
            ' ',
            '',
            ' ',
            self::SPACE_PLACEHOLDER . '<label',
            'span>' . self::SPACE_PLACEHOLDER,
            self::SPACE_PLACEHOLDER . '<span',
            'button>' . self::SPACE_PLACEHOLDER,
            self::SPACE_PLACEHOLDER . '<button',
            '><',
            '$1>',
            '=$1',
            ' ',
        ];

        $content = trim(preg_replace($search, $replace, $content));
    }

    private function extractCombinedInlineScripts(string &$content): string
    {
        if (strpos($content, '</script>') === false) {
            return '';
        }

        $jsContent = '';
        $index = 0;

        $content = preg_replace_callback('#<script>(.*?)</script>#s', function ($matches) use (&$jsContent, &$index) {
            ++$index;
            $scriptContent = trim($matches[1]);

            if (!str_ends_with($scriptContent, ';')) {
                $scriptContent .= ';';
            }

            $jsContent .= $scriptContent . PHP_EOL;

            return $index === 1 ? self::JAVASCRIPT_PLACEHOLDER : '';
        }, $content);

        $cacheItem = $this->cache->getItem(hash('xxh128', $jsContent));

        if ($cacheItem->isHit()) {
            return CacheCompressor::uncompress($cacheItem);
        }

        $jsContent = $this->minifyJavascript($jsContent);
        $cacheItem = CacheCompressor::compress($cacheItem, $jsContent);
        $cacheItem->expiresAfter(new \DateInterval('P1D'));
        $this->cache->save($cacheItem);

        return $jsContent;
    }

    private function minifySourceTypes(string &$content): void
    {
        $content = preg_replace([
            '/ type=["\']text\/javascript["\']/',
            '/ type=["\']text\/css["\']/'
        ], '', $content);
    }

    private function addCompressionHeader(
        ?ResponseHeaderBag $headerBag,
        string $content,
        int $lengthInitialContent,
        float $startTime
    ): void {
        $lengthContent = strlen($content);

        if ($lengthContent === 0) {
            return;
        }

        $savedData = round(100 - 100 / ($lengthInitialContent / $lengthContent), 2);
        $timeTook = (int) ((microtime(true) - $startTime) * 1000);

        $headerBag?->add(['X-Html-Compressor' => time() . ": {$savedData}% {$timeTook}ms"]);
    }
}
