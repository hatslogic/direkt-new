<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT37140;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\SearchKeyword\ProductSearchBuilderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[Package('framework')]
class ProductSearchBuilderDecorator implements ProductSearchBuilderInterface
{
    private const DEFAULT_TERM_MAX_LENGTH = 300;

    public function __construct(
        private readonly ProductSearchBuilderInterface $inner,
        private readonly LoggerInterface $logger,
        private readonly ParameterBagInterface $bag
    ) {
    }

    public function build(Request $request, Criteria $criteria, SalesChannelContext $context): void
    {
        $search = $request->get('search');

        $searchTermMaxLength = self::DEFAULT_TERM_MAX_LENGTH;

        if ($this->bag->has('shopware.search.term_max_length')) {
            $searchTermMaxLength = (int) $this->bag->get('shopware.search.term_max_length');
        }

        if (\is_array($search)) {
            $term = implode(' ', $search);
        } else {
            $term = (string) $search;
        }

        $term = trim($term);
        if (mb_strlen($term) > $searchTermMaxLength) {
            $this->logger->notice(
                'The search term "{term}" was trimmed because it exceeded the maximum length of {maxLength} characters.',
                ['term' => $term, 'maxLength' => $searchTermMaxLength]
            );

            $term = mb_substr($term, 0, $searchTermMaxLength);
        }

        $request->query->set('search', trim($term));
        $request->request->set('search', trim($term));

        $this->inner->build($request, $criteria, $context);
    }
}
