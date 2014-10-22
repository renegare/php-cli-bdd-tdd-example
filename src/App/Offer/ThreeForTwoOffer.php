<?php

namespace App\Offer;

use App\OfferInterface;

class ThreeForTwoOffer implements OfferInterface
{
    public function process(\SimpleXMLElement $xml) {
        $products = $xml->xpath('/order/products/product');

        usort($products, function($productA, $productB){
            $priceA = (float)$productA['price'];
            $priceB = (float)$productB['price'];

            if ($priceA == $priceB) {
                return 0;
            }
            return ($priceA < $priceB) ? -1 : 1;
        });

        $numberOfFreeProducts = floor(count($products) / 3);
        $productsToRemove = array_slice($products, 0, $numberOfFreeProducts);

        $currentTotal = (float) $xml->xpath('/order')[0]->total;
        foreach($productsToRemove as $product) {
            $currentTotal -= (float) $product['price'];
            $product['price'] = '0.00';
        }
        $xml->xpath('/order')[0]->total = $currentTotal;

        return $xml;
    }
}
