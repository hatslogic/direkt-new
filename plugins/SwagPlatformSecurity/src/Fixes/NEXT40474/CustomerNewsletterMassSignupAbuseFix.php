<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT40474;

use Shopware\Core\Checkout\Customer\Service\EmailIdnConverter;
use Shopware\Core\Framework\Log\Package;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Package('checkout')]
class CustomerNewsletterMassSignupAbuseFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-40474';
    }

    public static function getMinVersion(): string
    {
        return '6.5.0.0';
    }

    public static function getMaxVersion(): ?string
    {
        return '6.6.10.2';
    }

    public static function boot(ContainerInterface $container): void
    {
        if (class_exists(EmailIdnConverter::class, false)) {
            return;
        }

        include_once __DIR__ . '/EmailIdnConverter.php';
    }
}
