<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT40208;

use Shopware\Core\Checkout\Document\DocumentCollection;
use Shopware\Core\Checkout\Document\DocumentException;
use Shopware\Core\Checkout\Document\SalesChannel\AbstractDocumentRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FixDocumentRoute extends AbstractDocumentRoute
{
    /**
     * @param EntityRepository<DocumentCollection> $documentRepository
     */
    public function __construct(
        private readonly AbstractDocumentRoute $decorated,
        private readonly EntityRepository $documentRepository,
    ) {
    }

    public function getDecorated(): AbstractDocumentRoute
    {
        return $this->decorated;
    }

    public function download(
        string $documentId,
        Request $request,
        SalesChannelContext $context,
        string $deepLinkCode = '',
    ): Response {
        $this->checkAuth($documentId, $context);

        return $this->decorated->download($documentId, $request, $context, $deepLinkCode);
    }

    private function checkAuth(string $documentId, SalesChannelContext $context): void
    {
        $criteria = (new Criteria([$documentId]))
            ->addAssociations(['order.orderCustomer.customer', 'order.billingAddress']);

        $document = $this->documentRepository->search($criteria, $context->getContext())->first();
        if (!$document) {
            throw DocumentException::documentNotFound($documentId);
        }

        $order = $document->getOrder();
        if (!$order) {
            throw DocumentException::orderNotFound($document->getOrderId());
        }

        $orderCustomer = $order->getOrderCustomer();
        if (!$orderCustomer) {
            throw DocumentException::generationError();
        }

        if ($context->getCustomer() !== null && $orderCustomer->getCustomerId() !== $context->getCustomer()->getId()) {
            throw DocumentException::generationError();
        }
    }
}
