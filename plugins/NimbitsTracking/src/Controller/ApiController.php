<?php

namespace Nimbits\NimbitsTracking\Controller;

use Doctrine\DBAL\Connection;
use Nimbits\NimbitsTracking\Service\SettingService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\TermsAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\SumAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class ApiController extends AbstractController
{
    private EntityRepository $customerRepository;
    private EntityRepository $orderRepository;
    private SettingService $settings;
    private Connection $connection;

    public function __construct(
        Connection       $connection,
        SettingService   $settingService,
        EntityRepository $customerRepository,
        EntityRepository $orderRepository
    )
    {
        $this->connection = $connection;
        $this->settings = $settingService;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
    }

    #[Route(path: '/api/nimbits/tracking/registrations', name: 'api.action.nimbits.tracking.registrations', methods: ['POST'])]
    public function getRegistrationsRoute(Request $request): Response
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $result = [
            'registrations' => []
        ];

        foreach ($this->getRegistrations($startDate, $endDate)->getBuckets() as $bucket) {
            if (!empty($bucket->getKey()) || $this->settings->getSetting('emptyReferrer')) {
                $result['registrations'][] = [
                    "x" => ucfirst($bucket->getKey()),
                    "y" => $bucket->getCount()
                ];
            }
        }

        return new JsonResponse([
            'success' => true,
            'result' => $result
        ]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return AggregationResult
     */
    private function getRegistrations($startDate, $endDate): AggregationResult
    {
        return $this->customerRepository->search(
            (new Criteria())
                ->addAggregation(new TermsAggregation('registrations', 'customFields.nb_tracking_partner'))
                ->addFilter(new RangeFilter('createdAt', [
                    RangeFilter::GTE => $startDate,
                    RangeFilter::LTE => $endDate
                ]))
                ->setLimit(1),
            Context::createDefaultContext()
        )->getAggregations()->get('registrations');
    }

    #[Route(path: '/api/nimbits/tracking/orders', name: 'api.action.nimbits.tracking.orders', methods: ['POST'])]
    public function getOrdersRoute(Request $request): Response
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $result = [
            'orders' => [],
            'sales' => []
        ];

        foreach ($this->getOrders($startDate, $endDate)->getAggregations()->get('orders')->getBuckets() as $bucket) {
            if (!empty($bucket->getKey()) || $this->settings->getSetting('emptyReferrer')) {
                $result['orders'][] = [
                    "x" => ucfirst($bucket->getKey()),
                    "y" => $bucket->getCount()
                ];

                $result['sales'][] = [
                    "x" => ucfirst($bucket->getKey()),
                    "y" => $bucket->getResult()->getSum()
                ];
            }
        }

        return new JsonResponse([
            'success' => true,
            'result' => $result
        ]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return \Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult
     */
    private function getOrders($startDate, $endDate): EntitySearchResult
    {
        return $this->orderRepository->search(
            (new Criteria())
                ->addAggregation(new TermsAggregation(
                    'orders',
                    'customFields.nb_tracking_partner',
                    null,
                    null,
                    new SumAggregation('total', $this->settings->getSetting('useTotal') == 'total' ? 'amountTotal' : 'amountNet')
                ))
                ->addFilter(new RangeFilter('createdAt', [
                    RangeFilter::GTE => $startDate,
                    RangeFilter::LTE => $endDate
                ]))
                ->setLimit(1),
            Context::createDefaultContext()
        );
    }

    #[Route(path: '/api/nimbits/tracking/visitors', name: 'api.action.nimbits.tracking.visitors', methods: ['POST'])]
    public function getVisitorsRoute(Request $request): Response
    {
        $startDate = $request->get('startDate', date('Y-m-d'));
        $endDate = $request->get('endDate', date('Y-m-d'));
        $result = [
            'visitors' => [],
            'requests' => []
        ];

        foreach ($this->getVisitors($startDate, $endDate) as $row) {
            if (!empty($row['referrer']) || $this->settings->getSetting('emptyReferrer')) {
                $result['visitors'][] = [
                    "x" => ucfirst($row['referrer']),
                    "y" => intval($row['visitors'])
                ];

                $result['requests'][] = [
                    "x" => ucfirst($row['referrer']),
                    "y" => intval($row['requests'])
                ];
            }
        }

        return new JsonResponse([
            'success' => true,
            'result' => $result
        ]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function getVisitors($startDate, $endDate): array
    {

        $qb = $this->connection->createQueryBuilder()
            ->select('referrer', 'SUM(visitors) AS visitors', 'SUM(requests) AS requests')
            ->from('nb_tracking_visitors')
            ->where('day >= :startDate')
            ->andWhere('day <= :endDate');

        if ($this->settings->getSetting('emptyReferrer') == false) {
            $qb->andWhere('referrer != ""');
        }

        $qb->groupBy('referrer');

        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);

        return $ds = $qb->execute()
            ->fetchAllAssociative();
    }

    #[Route(path: '/api/nimbits/tracking/export-csv', name: 'api.action.nimbits.tracking.export.csv', methods: ['POST'])]
    public function exportCsvRoute(Request $request): Response
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $statistics = [];

        $registrations = $this->getRegistrations($startDate, $endDate);
        $orders = $this->getOrders($startDate, $endDate)->getAggregations()->get('orders');

        foreach ($registrations->getBuckets() as $registration) {
            $statistics[$registration->getKey()]['registrations'] = $registration->getCount();
        }

        foreach ($orders->getBuckets() as $order) {
            $statistics[$order->getKey()]['orders'] = $order->getCount();
            $statistics[$order->getKey()]['sales'] = $order->getResult()->getSum();
        }

        foreach ($this->getVisitors($startDate, $endDate) as $row) {
            $statistics[$row['referrer']]['visitors'] = $row['visitors'];
            $statistics[$row['referrer']]['requests'] = $row['requests'];
        }

        $filename = $request->getHost() . '-' . str_replace('-', '_', $startDate) . '-' . str_replace('-', '_', $endDate) . '.csv';

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->setContent($this->genCSV($statistics));

        return $response;
    }

    private function genCSV(array $statistics): string
    {
        $csvStream = fopen('php://memory', 'r+');

        fputcsv($csvStream, [
            'partner',
            'registrations',
            'orders',
            'sales',
            'visitors',
            'requests'
        ]);

        foreach ($statistics as $partner => $details) {
            if (!empty($partner) || $this->settings->getSetting('emptyReferrer')) {
                fputcsv($csvStream, [
                    empty($partner) ? '-' : $partner,
                    $details['registrations'] ?? 0,
                    $details['orders'] ?? 0,
                    $details['sales'] ?? 0,
                    $details['visitors'] ?? 0,
                    $details['requests'] ?? 0
                ]);
            }
        }
        rewind($csvStream);
        $csv = stream_get_contents($csvStream);
        fclose($csvStream);

        return $csv;
    }
}
