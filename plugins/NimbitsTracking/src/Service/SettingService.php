<?php

namespace Nimbits\NimbitsTracking\Service;

use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SettingService
{
    public const SYSTEM_CONFIG_DOMAIN = 'NimbitsTracking.config.';
    private SystemConfigService $systemConfigService;

    public function __construct(
        SystemConfigService $systemConfigService
    )
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getSetting(string $key, ?string $salesChannelId = null)
    {
        return $this->systemConfigService->get(
            self::SYSTEM_CONFIG_DOMAIN . $key,
            $salesChannelId
        );
    }

    public function getSettingsAsArray(?string $salesChannelId = null): array
    {
        $values = $this->systemConfigService->getDomain(
            self::SYSTEM_CONFIG_DOMAIN,
            $salesChannelId,
            true
        );

        $indexedValues = [];

        foreach ($values as $key => $value)
        {
            $property = substr($key, strlen(self::SYSTEM_CONFIG_DOMAIN));
            $indexedValues[$property] = $value;
        }

        return $indexedValues;
    }

    public function getSettingsAsStruct(?string $salesChannelId = null): ArrayStruct
    {
        $settings = $this->getSettingsAsArray($salesChannelId);

        return new ArrayStruct($settings);
    }
}