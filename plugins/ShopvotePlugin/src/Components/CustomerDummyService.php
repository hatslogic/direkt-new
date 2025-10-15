<?php

namespace Shopvote\ShopvotePlugin\Components;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\EntityNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Uuid\Uuid;

class CustomerDummyService
{
    /** @var EntityRepository */
    private $customerRepository;

    /** @var EntityRepository */
    private $paymentMethodRepository;

    /** @var EntityRepository */
    private $salutationRepository;

    /** @var EntityRepository */
    private $salesChannelRepository;

    /** @var EntityRepository */
    private $customerGroupRepository;

    public function __construct(
        EntityRepository $customerRepository,
        EntityRepository $paymentMethodRepository,
        EntityRepository $salutationRepository,
        EntityRepository $salesChannelRepository,
        EntityRepository $customerGroupRepository
    )
    {
        $this->customerRepository      = $customerRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->salutationRepository    = $salutationRepository;
        $this->salesChannelRepository  = $salesChannelRepository;
        $this->customerGroupRepository = $customerGroupRepository;
    }

    public function getShopvoteUserID()
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('firstName', 'SHOPVOTE'));
        $criteria->setLimit(1);

        $entity = $this->customerRepository->search($criteria,
            Context::createDefaultContext())->getEntities()->first();
        return $entity->get("id");
    }

    public function getActivePaymentMethod()
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', 1));
        $criteria->setLimit(1);

        $entity = $this->paymentMethodRepository->search($criteria,
            Context::createDefaultContext())->getEntities()->first();

        return $entity->get("id");
    }

    public function getActiveSalutation()
    {
        $criteria = (new Criteria())->setLimit(1);

        return $this->salutationRepository->searchIds($criteria, Context::createDefaultContext())->getIds()[0];
    }

    public function addShopvoteCustomer()
    {
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', 1));
        $criteria->setLimit(1);

        $activeSalesChannelId = $this->salesChannelRepository->searchIds(
                $criteria,
                $context
            )
            ->firstId();

        if ($activeSalesChannelId === null) {
            throw new EntityNotFoundException('SalesChannel', 'any active');
        }

        $customerId = Uuid::randomHex();
        $password   = Uuid::randomHex();

        $this->customerRepository->create(
            [
                [
                    'id' => $customerId,
                    'salesChannelId' => $activeSalesChannelId,
                    'defaultShippingAddress' =>
                        [
                            'firstName' => 'n/a',
                            'lastName' => 'n/a',
                            'city' => 'n/a',
                            'street' => 'n/a',
                            'zipcode' => 'n/a',
                            'salutationId' => $this->getActiveSalutation(),
                            'country' => ['name' => 'n/a'],
                        ],
                    'defaultPaymentMethodId' => $this->getActivePaymentMethod(),
                    'groupId' => $this->getCustomerGroupId($context),
                    'email' =>  'shopware6dummy@shopvote.de',
                    'password' => $password,
                    'lastName' => 'User',
                    'firstName' => 'SHOPVOTE',
                    'salutationId' => $this->getActiveSalutation(),
                    'customerNumber' => 'n/a'
                ]
            ],
            $context
        );
    }

    private function getCustomerGroupId(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addSorting(new FieldSorting('createdAt'));
        $criteria->setLimit(1);

        $firstId = $this->customerGroupRepository->searchIds($criteria, $context)->firstId();

        if ($firstId === null) {
            throw new EntityNotFoundException('CustomerGroup', 'any');
        }

        return $firstId;
    }
}
