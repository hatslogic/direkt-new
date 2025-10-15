<?php

declare(strict_types=1);

namespace WebLa_ContactFormFileUpload;

use Shopware\Core\Framework\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class WebLa_ContactFormFileUpload extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        if ($this->isCMSExtPluginInstalled($container)) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
            $loader->load('custom_form.xml');
        }
    }

    private function isCMSExtPluginInstalled(ContainerBuilder $container): bool
    {
        return true;
    }
}
