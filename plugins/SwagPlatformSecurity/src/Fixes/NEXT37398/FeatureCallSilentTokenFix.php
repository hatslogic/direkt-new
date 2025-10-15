<?php

namespace Swag\Security\Fixes\NEXT37398;

use Shopware\Core\Framework\Adapter\Twig\Node\FeatureCallSilentToken;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FeatureCallSilentTokenFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-37398';
    }

    public static function getMinVersion(): string
    {
        return '6.4.5.0';
    }

    public static function getMaxVersion(): string
    {
        return '6.6.5.1';
    }

    public static function boot(ContainerInterface $container): void
    {
        if (class_exists(FeatureCallSilentToken::class, false)) {
            return;
        }

        include_once __DIR__ . '/FeatureCallSilentToken.php';
    }
}
