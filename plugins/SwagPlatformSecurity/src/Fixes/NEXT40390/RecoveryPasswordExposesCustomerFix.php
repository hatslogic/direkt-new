<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT40390;

use Shopware\Core\Framework\Log\Package;
use Swag\Security\Components\AbstractSecurityFix;

#[Package('checkout')]
class RecoveryPasswordExposesCustomerFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-40390';
    }

    public static function getMinVersion(): string
    {
        return '6.1.0';
    }

    public static function getMaxVersion(): ?string
    {
        return '6.6.10.2';
    }
}
