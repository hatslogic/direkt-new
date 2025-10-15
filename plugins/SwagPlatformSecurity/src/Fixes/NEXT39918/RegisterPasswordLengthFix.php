<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT39918;

use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Framework\Validation\BuildValidationEvent;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;

class RegisterPasswordLengthFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-39918';
    }

    public static function getMinVersion(): string
    {
        return '6.1.0';
    }

    public static function getMaxVersion(): ?string
    {
        return '6.6.10.2';
    }

    public function onBuildValidation(BuildValidationEvent $event): void
    {
        $definition = $event->getDefinition();
        $properties = $definition->getProperties();

        if (!\array_key_exists('password', $properties)) {
            return;
        }

        foreach ($properties['password'] as $constraint) {
            if (!$constraint instanceof Length) {
                continue;
            }

            $constraint->max = PasswordHasherInterface::MAX_PASSWORD_LENGTH;
            $constraint->maxMessage = 'VIOLATION::PASSWORD_IS_TOO_LONG';
        }
    }

    public static function buildContainer(ContainerBuilder $container): void
    {
        $container->getDefinition(AccountService::class)->setClass(PatchedAccountService::class);
    }
}
