<?php

namespace Swag\Security\Fixes\NEXT37527;

use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SalesChannelVisibilityFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-37527';
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
        if (class_exists(SalesChannelRepository::class, false) || \PHP_SAPI === 'cli') {
            return;
        }

        include_once __DIR__ . '/SalesChannelRepository.php';
    }
}
