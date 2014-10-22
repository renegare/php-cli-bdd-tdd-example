<?php

namespace spec\App\Offer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ShampooGetConditionerHalfPriceOfferSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('App\Offer\ShampooGetConditionerHalfPriceOffer');
        $this->shouldHaveType('App\OfferInterface');
    }

    function it_should_discount_conditioner() {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="Shampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="5.50" category="Conditioner"/>
    </products>
    <total>10.49</total>
</order>
EOF
;
        $this->process(new \SimpleXMLElement($xml))->shouldEqualXML(new \SimpleXMLElement(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="Shampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="2.75" category="Conditioner"/>
    </products>
    <total>7.74</total>
</order>
EOF
));
    }

    function it_should_give_no_discount() {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="NotShampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="5.50" category="Conditioner"/>
    </products>
    <total>10.49</total>
</order>
EOF
;
        $this->process(new \SimpleXMLElement($xml))->shouldEqualXML(new \SimpleXMLElement(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="NotShampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="5.50" category="Conditioner"/>
    </products>
    <total>10.49</total>
</order>
EOF
));
    }

    function it_should_discount_only_2_conditioners() {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="Shampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="5.50" category="Conditioner"/>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="Shampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="5.50" category="Conditioner"/>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="NotShampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="5.50" category="Conditioner"/>
    </products>
    <total>31.47</total>
</order>
EOF
;
        $this->process(new \SimpleXMLElement($xml))->shouldEqualXML(new \SimpleXMLElement(<<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="Shampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="2.75" category="Conditioner"/>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="Shampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="2.75" category="Conditioner"/>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99" category="NotShampoo"/>
        <product title="L'Oréal Paris Hair Conditioner 250ml" price="5.50" category="Conditioner"/>
    </products>
    <total>25.97</total>
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
