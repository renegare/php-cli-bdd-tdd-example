<?php

namespace App;

class Processor
{
    protected $offers = [];

    public function process(\SimpleXMLElement $xml, $offerCodes=[]) {
        foreach($offerCodes as $offerCode) {
            if(!isset($this->offers[$offerCode])) {
                throw new \OutOfRangeException('Unknown offer code: ' . $offerCode);
            }

            $offer = $this->offers[$offerCode];
            $xml = $offer->process($xml);
        }

        return $xml;
    }

    public function registerOffer($code, OfferInterface $offer) {
        $this->offers[$code] = $offer;
    }
}
