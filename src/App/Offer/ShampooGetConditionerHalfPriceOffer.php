<?php

namespace App\Offer;

use App\OfferInterface;

class ShampooGetConditionerHalfPriceOffer implements OfferInterface
{
    const CAT_CONDITIONER = 'conditioner';
    const CAT_SHAMPOO = 'shampoo';

    public function process(\SimpleXMLElement $xml) {
        $shampoos = [];
        $conditioners = [];

        foreach($xml->xpath('/order/products/product') as $product) {
            switch(strtolower($product['category'])) {
                case self::CAT_SHAMPOO:
                    $shampoos[] = $product;
                    break;
                case self::CAT_CONDITIONER:
                    $conditioners[] = $product;
                    break;
            }
        }

        $eligableForDiscount = array_splice($conditioners, 0, count($shampoos));

        $discountAmount = array_reduce($eligableForDiscount, function($discountAmount, $conditioner) {
            $discount = (float)$conditioner['price'] * .5;
            $conditioner['price'] = $discount;
            return $discountAmount + $discount;
        }, 0.00);

        $total = (float) $xml->xpath('/order')[0]->total;
        $xml->xpath('/order')[0]->total = (float) $total - $discountAmount;

        return $xml;
    }
}
