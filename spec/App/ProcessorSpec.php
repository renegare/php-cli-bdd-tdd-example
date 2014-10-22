<?php

namespace spec\App;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use org\bovigo\vfs\vfsStream;
use App\OfferInterface;

class ProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('App\Processor');
    }

    function it_should_return_xml() {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Rimmel Lasting Finish Lipstick 4g" price="4.99"></product>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99"></product>
    </products>
    <total>9.98</total>
</order>

EOF
;
        $this->process(new \SimpleXMLElement($xml))->shouldEqualXML(new \SimpleXMLElement($xml));
    }

    /**
     * @param App\OfferInterface $offer
     */
    function it_should_process_order_with_registered_offer(OfferInterface $offer) {
        $xmlContent = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Rimmel Lasting Finish Lipstick 4g" price="4.99"/>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99"/>
    </products>
    <total>9.98</total>
</order>

EOF
;
        $xml = new \SimpleXMLElement($xmlContent);
        $modifiedXml = new \SimpleXMLElement($xmlContent);
        $modifiedXml->xpath('/order')[0]->total = '1.23';
        $offer->process($xml)
            ->shouldBeCalled()
            ->willReturn($modifiedXml);

        $this->registerOffer('o1', $offer);
        $this->process($xml, ['o1'])->shouldEqualXML($modifiedXml);
    }

    function it_should_throw_out_of_range_exception_for_unknown_offers() {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<order>
    <products>
        <product title="Rimmel Lasting Finish Lipstick 4g" price="4.99"></product>
        <product title="Sebamed Anti-Dandruff Shampoo 200ml" price="4.99"></product>
    </products>
    <total>9.98</total>
</order>

EOF
;
        $this->shouldThrow('OutOfRangeException')->duringProcess(new \SimpleXMLElement($xml), ['u1']);
    }

    public function getMatchers() {
        return [
            'equalXML' => function(\SimpleXMLElement $subject, \SimpleXMLElement $value) {
                return $subject->asXML() === $value->asXML();
            }
        ];
    }
}
