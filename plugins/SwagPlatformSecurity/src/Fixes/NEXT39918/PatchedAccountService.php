<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT39918;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\CustomerException;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;

class PatchedAccountService extends AccountService
{
    use CheckPasswordLengthTrait;

    public function getCustomerByLogin(string $email, string $password, SalesChannelContext $context): CustomerEntity
    {
        if ($this->isPasswordTooLong($password)) {
            throw CustomerException::badCredentials();
        }

        return parent::getCustomerByLogin($email, $password, $context);
    }
}

