<?php

namespace Swag\Security\Fixes\NEXT37397;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InvalidAggregationQueryException;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\SearchRequestException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Aggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\BucketAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Parser\AggregationParser;

class PatchedAggregationParser extends AggregationParser
{
    public function buildAggregations(EntityDefinition $definition, array $payload, Criteria $criteria, SearchRequestException $searchRequestException): void
    {
        parent::buildAggregations($definition, $payload, $criteria, $searchRequestException);

        foreach ($criteria->getAggregations() as $i => $aggregation) {
            $this->validateAggregation($aggregation, $searchRequestException);
        }
    }

    private function validateAggregation(Aggregation $aggregation, SearchRequestException $searchRequestException): void
    {
        if (str_contains($aggregation->getName(), '?') || str_contains($aggregation->getName(), ':')) {
            $searchRequestException->add(new InvalidAggregationQueryException('Invalid aggregation name'), '/aggregations/');
        }

        if ($aggregation instanceof BucketAggregation && $aggregation->getAggregation() !== null) {
            $this->validateAggregation($aggregation->getAggregation(), $searchRequestException);
        }
    }

}
