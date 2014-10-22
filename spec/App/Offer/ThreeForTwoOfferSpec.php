<?php

namespace spec\App\Offer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ThreeForTwoOfferSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('App\Offer\ThreeForTwoOffer');
        $this->shouldHaveType('App\OfferInterface');
    }

    function it_should_make_the_cheapest_item_free() {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Rimmel Lasting Finish Lipstick 4g" price="4.99" category="Lipstick"/>
        <product title="bareMinerals Marvelous Moxie Lipstick 3.5g" price="13.95" category="Lipstick"/>
        <product title="Rimmel Kate Lasting Finish Matte Lipstick" price="5.49" category="Lipstick"/>
    </products>
    <total>24.43</total>
</order>
EOF
;
        $this->process(new \SimpleXMLElement($xml))->shouldEqualXML(new \SimpleXMLElement(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Rimmel Lasting Finish Lipstick 4g" price="0.00" category="Lipstick"/>
        <product title="bareMinerals Marvelous Moxie Lipstick 3.5g" price="13.95" category="Lipstick"/>
        <product title="Rimmel Kate Lasting Finish Matte Lipstick" price="5.49" category="Lipstick"/>
    </products>
    <total>19.44</total>
</order>
EOF
));
    }

    function it_should_make_the_cheapest_items_free() {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Rimmel Lasting Finish Lipstick 4g" price="4.99"></product>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99"></product>
        <product title="Sebamed Anti-Dandruff Shampoo 100ml" price="3.99"></product>
        <product title="A Different Rimmel Lasting Finish Lipstick 4g" price="8.99"></product>
        <product title="A Different Sebamed Anti-Dandruff Shampoo 200ml" price="6.99"></product>
        <product title="A Different Sebamed Anti-Dandruff Shampoo 100ml" price="2.99"></product>
    </products>
    <total>32.94</total>
</order>
EOF
;
        $this->process(new \SimpleXMLElement($xml))->shouldEqualXML(new \SimpleXMLElement(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Rimmel Lasting Finish Lipstick 4g" price="4.99"></product>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99"></product>
        <product title="Sebamed Anti-Dandruff Shampoo 100ml" price="0.00"></product>
        <product title="A Different Rimmel Lasting Finish Lipstick 4g" price="8.99"></product>
        <product title="A Different Sebamed Anti-Dandruff Shampoo 200ml" price="6.99"></product>
        <product title="A Different Sebamed Anti-Dandruff Shampoo 100ml" price="0.00"></product>
    </products>
    <total>25.96</total>
</order>
EOF
));
    }

    public function getMatchers() {
        return [
            'equalXML' => function(\SimpleXMLElement $subject, \SimpleXMLElement $value) {
                return $subject->asXML() === $value->asXML();
            }
        ];
    }
}
