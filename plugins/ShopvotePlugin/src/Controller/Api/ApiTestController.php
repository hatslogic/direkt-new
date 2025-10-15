<?php

namespace Shopvote\ShopvotePlugin\Controller\Api;

use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Framework\Log\Package;

#[Route(defaults: ['_routeScope' => ['administration']])]
#[Package('core')]

class ApiTestController
{
    #[Route(path: '/api/v{version}/_action/shopvote-plugin/verify', methods: ['POST'])]
    public function check(RequestDataBag $dataBag): JsonResponse
    {
        $apiKey = $dataBag->get('ShopvotePlugin.config.apiKey');
        $apiSecret = $dataBag->get('ShopvotePlugin.config.apiKeySecret');
        $shopId = $dataBag->get('ShopvotePlugin.config.shopId');
        $headers = [
            "Apikey: " . $apiKey,
            "Apisecret: " . $apiSecret,
            "User-Agent:  App.GDX." . $shopId
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.shopvote.de/auth');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);

        $json = json_decode($res, true);
        if($json["Code"] != 200)
        {
            $success = false;
        }else{
            $success=true;
        }

        curl_close($ch);

        return new JsonResponse(['success' => $success]);

    }
}
