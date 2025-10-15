<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Service;

use Symfony\Component\HttpFoundation\Response;
use HatslogicPageOptimisation\Service\ConfigService;

class PreloadLinkService
{
    private ConfigService $configService;

    private ?Response $response;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function setResponse(Response $response): PreloadLinkService
    {
        $this->response = $response;
        return $this;
    }

    public function optimize(): void
    {
        $response = $this->response;
        $content = $response->getContent();
        if ($content === false) {
            return;
        }

        $lengthInitialContent = mb_strlen($content, 'utf8');
        if ($lengthInitialContent === 0) {
            return;
        }

        if (!$this->configService->isPreloadEnable()) {
            return;
        }

        $content = $this->modify($content);
        $response->setContent($content);
    }

    private function modify(string $html): string
    {
        $preloads = [];

        $links = $this->configService->getCustomPreloadLinks();
        foreach($links as $link) {
            if (!filter_var($link, FILTER_VALIDATE_URL)) {
                continue;
            }
            $extension = \pathinfo($link, PATHINFO_EXTENSION);
            if (!$extension) {
                continue;
            }
            $as = $this->getAsByExtension($extension);
            if (!$as) {
                continue;
            }
            $link = str_replace(['http://', 'https://'], '//', $link);
            $attributes = [
                'rel' => 'preload',
                'href' => $link,
                'as' => $as,
                'type' => $as . '/' . $extension,
                'crossorigin' => 'anonymous'
            ];
            $attrs = '';
            foreach($attributes as $attributeName => $attributeValue) {
                $attrs .= " {$attributeName}=\"{$attributeValue}\"";
            }

            $preloads[] = "<link {$attrs} />";
        }

        $preload = implode('', $preloads);
        $html = str_replace('</head>', $preload . '</head>', $html);
        return $html;
    }

    private function getAsByExtension($extension)
    {
        $as = '';
        switch ($extension) {
            case 'js':
                $as = 'script';
                break;
            case 'css':
                $as = 'style';
                break;
            case 'eot':
            case 'otf':
            case 'ttf':
            case 'woff':
            case 'woff2':
                $as = 'font';
                break;
            case 'ico':
            case 'webp':
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'bmp':
            case 'svg':
            case 'png':
                $as = 'image';
                break;
        }

        return $as;
    }
}
