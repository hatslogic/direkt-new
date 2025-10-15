<?php

namespace Nimbits\NimbitsTracking\Controller;

use Doctrine\DBAL\Connection;
use Nimbits\NimbitsTracking\Service\SettingService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\TermsAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\SumAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('storefront')]
class GoogleDataController extends StorefrontController
{
    private SalesChannelRepository $salesChannelProductRepository;
    private SettingService $settings;
    private Connection $connection;

    public function __construct(
        Connection                $connection,
        SettingService            $settingService,
        SalesChannelRepository $salesChannelProductRepository
    )
    {
        $this->connection = $connection;
        $this->settings = $settingService;
        $this->salesChannelProductRepository = $salesChannelProductRepository;
    }

    #[Route(path: '/nimbits/tracking/productdata', name: 'frontend.nimbits.tracking.productdata', options: ['seo' => false], defaults: ['XmlHttpRequest' => true], methods: ['POST'])]
    public function productData(Request $request, SalesChannelContext $salesChannelContext): Response
    {
        $productIds = $request->get('productIds');
        if(count($productIds) > 100){
            return new JsonResponse([
                'success' => false,
                'result' => null,
                'msg' => "Requested too much products, max 100 per request",
            ]);
        }


        //search products
        $crit = new Criteria();
        $crit->addFilter(new EqualsAnyFilter('id', $productIds));

        $crit->setLimit(100);

        $productData = $this->salesChannelProductRepository->search(
            $crit,
            $salesChannelContext
        );

        return new JsonResponse([
            'success' => true,
            'result' => array_values($productData->getElements()),
            'currency' => $salesChannelContext->getCurrency()
        ]);
    }
}