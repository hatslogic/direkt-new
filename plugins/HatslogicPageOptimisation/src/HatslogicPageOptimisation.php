<?php declare(strict_types=1);

namespace HatslogicPageOptimisation;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use HatslogicPageOptimisation\Service\CustomFieldsInstaller;

class HatslogicPageOptimisation extends Plugin
{
    public function install(InstallContext $installContext): void
    {

        parent::install($installContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        // Remove or deactivate the data created by the plugin
    }

    public static function getPluginDir(): string
    {
        return __DIR__;
    }

    public function executeComposerCommands(): bool
    {
        return true;
    }
}
