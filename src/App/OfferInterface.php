<?php

namespace App;

interface OfferInterface
{
    public function process(\SimpleXMLElement $xml);
}
