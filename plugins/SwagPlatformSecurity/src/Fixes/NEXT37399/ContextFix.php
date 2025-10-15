<?php

namespace Swag\Security\Fixes\NEXT37399;

use Shopware\Core\Framework\Context;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContextFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-37399';
    }

    public static function getMinVersion(): string
    {
        return '6.1.0';
    }

    public static function getMaxVersion(): string
    {
        return '6.6.5.1';
    }

    public static function boot(ContainerInterface $container): void
    {
        if (class_exists(Context::class, false)) {
            return;
        }

        include_once __DIR__ . '/Context.php';
    }
}
