<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT40208;

use Shopware\Core\Framework\Log\Package;
use Swag\Security\Components\AbstractSecurityFix;

#[Package('checkout')]
class CustomersCanAccessOthersDocumentsFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-40208';
    }

    public static function getMinVersion(): string
    {
        return '6.4.14';
    }

    public static function getMaxVersion(): ?string
    {
        return '6.6.10.2';
    }
}
