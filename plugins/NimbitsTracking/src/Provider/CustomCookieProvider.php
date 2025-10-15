<?php

namespace Nimbits\NimbitsTracking\Provider;

use Nimbits\NimbitsTracking\Service\SettingService;
use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;

class CustomCookieProvider implements CookieProviderInterface
{
    private CookieProviderInterface $originalService;
    private SettingService $settings;

    public function __construct(
        CookieProviderInterface $service,
        SettingService          $settingService
    )
    {
        $this->originalService = $service;
        $this->settings = $settingService;
    }

    public function getCookieGroups(): array
    {
        $cookies = $this->originalService->getCookieGroups();

        if ($this->settings->getSetting('enableCookie')) {
            foreach (array_keys($cookies) as $cookieEntry) {
                if ($cookies[$cookieEntry]['snippet_name'] == $this->settings->getSetting('cookieType')) {
                    $cookies[$cookieEntry]['entries'][] = [
                        'snippet_name' => 'nimbits.tracking.cookie.name',
                        'snippet_description' => 'nimbits.tracking.cookie.description',
                        'cookie' => $this->settings->getSetting('cookieName') . '_enabled',
                        'value' => 'true'
                    ];
                    break;
                }
            }
        }

        return $cookies;
    }
}