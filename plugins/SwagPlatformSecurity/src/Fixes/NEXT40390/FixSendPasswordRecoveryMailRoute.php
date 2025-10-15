<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT40390;

use Shopware\Core\Checkout\Customer\CustomerException;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractSendPasswordRecoveryMailRoute;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SuccessResponse;

#[Package('checkout')]
class FixSendPasswordRecoveryMailRoute extends AbstractSendPasswordRecoveryMailRoute
{
    public function __construct(
        private readonly AbstractSendPasswordRecoveryMailRoute $decorated,
    ) {
    }

    public function getDecorated(): AbstractSendPasswordRecoveryMailRoute
    {
        return $this->decorated;
    }

    public function sendRecoveryMail(RequestDataBag $data, SalesChannelContext $context, bool $validateStorefrontUrl = true): SuccessResponse
    {
        try {
            return $this->getDecorated()->sendRecoveryMail($data, $context, $validateStorefrontUrl);
        } catch (CustomerException) {
            return new SuccessResponse();
        }
    }
}
