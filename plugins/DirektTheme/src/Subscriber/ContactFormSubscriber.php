<?php

namespace DirektTheme\Subscriber;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Currency\CurrencyFormatter;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactFormSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CartService $cartService,
        private EntityRepository $productRepository,
        private CurrencyFormatter $currencyFormatter
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            NavigationPageLoadedEvent::class => 'onCmsPageLoaded',
        ];
    }

    public function onCmsPageLoaded(NavigationPageLoadedEvent $event): void
    {
        $originalRequestUri = $event->getRequest()->attributes->get('sw-original-request-uri');

        if (strpos($originalRequestUri, 'product-enquiry') === false) {
            return;
        }

        $productId = $event->getRequest()->query->get('product');
        $enquiryType = $event->getRequest()->query->get('type');

        if ($enquiryType === 'cart') {
            $cart = $this->cartService->getCart($event->getSalesChannelContext()->getToken(), $event->getSalesChannelContext());

            $text = '';
            foreach ($cart->getLineItems() as $lineItem) {

                if ($lineItem->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
                    continue;
                }

                $text .= $lineItem->getQuantity() . ' x ' . $lineItem->getLabel();

                $options = $lineItem->getPayloadValue('options');

                if (!empty($options)) {
                    foreach ($options as $option) {
                        $text .= ' - ' . $option['group'] . ': ' . $option['option'];
                    }
                }

                $itemNumber = $lineItem->getPayloadValue('productNumber');

                $text .= ' (' . $itemNumber . ') ';

                $amount = $this->currencyFormatter->formatCurrencyByLanguage($lineItem->getPrice()->getTotalPrice(), $event->getSalesChannelContext()->getCurrency()->getIsoCode(), $event->getSalesChannelContext()->getLanguageId(), $event->getContext());

                $text .= ' - ' . $amount . PHP_EOL;
            }

            $page = $event->getPage();
            $page->addExtension('productEnquiryData', new ArrayStruct([
                'cartText' => $text,
            ]));

            return;
        }

        if (empty($productId)) {
            return;
        }

        if (!Uuid::isValid($productId)) {
            return;
        }

        $context = $event->getContext();
        $product = $this->getProductById($productId, $context);

        if (!$product) {
            return;
        }

        $text = $product->getTranslated()['name'];

        $options = $product->getOptions();

        if (!empty($options)) {
            foreach ($options as $option) {
                $text .= ' - ' . $option->getGroup()->getName() . ': ' . $option->getName();
            }
        }

        $itemNumber = $product->getProductNumber();

        $text .= ' (' . $itemNumber . ') ';

        $page = $event->getPage();
        $page->addExtension('productEnquiryData', new ArrayStruct([
            'productText' => $text,
        ]));

        return;
        // dd($page);
    }

    private function getProductById(string $productId, Context $context): ?ProductEntity
    {
        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('options.group');

        return $this->productRepository->search($criteria, $context)->first();
    }
}
