<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OptimizationTwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('file_exists', [$this, 'checkFileExists']),
        ];
    }

    public function checkFileExists(string $filePath)
    {
        $headers = @get_headers($filePath);
        return $headers && strpos($headers[0], '200') !== false;
    }
}
