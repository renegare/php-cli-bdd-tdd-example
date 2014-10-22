<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Console\Tester\ApplicationTester;
use App\Console;

require_once 'vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    protected $enabledOffers = [];
    protected $rootFS;
    protected $orderFile;

    /**
     * @Given the :arg1 offer is enabled
     */
    public function theOfferIsEnabled($arg1)
    {
        $code = $this->getOfferCode($arg1);
        if(!in_array($code, $this->enabledOffers)) {
            $this->enabledOffers[] = $code;
        }
    }

    /**
     * @When the following products are put on the order
     */
    public function theFollowingProductsArePutOnTheOrder(TableNode $table)
    {
        $xml = new \SimpleXMLElement('<order/>');
        $products = $xml->addChild('products');

        $total = 0;
        foreach ($table as $row) {
            $product = $products->addChild('product');
            $product->addAttribute('title', $row['Title']);
            $product->addAttribute('price', $row['Price']);
            $product->addAttribute('category', $row['Category']);
            $total += (float) $row['Price'];
        }

        $xml->addChild('total');
        $xml->total = $total;
        $this->orderFile = $this->saveTmpFile($xml->asXML(), '.xml');
        assertFileExists($this->orderFile);
    }

    /**
     * @Then I should get the :productLabel for free
     */
    public function iShouldGetTheForFree($productLabel)
    {
        $this->processOrder();
        $product = $this->getProduct($productLabel);
        assertEquals('0.00', (string) $product['price']);
    }

    /**
     * @Then the order total should be :arg1
     */
    public function theOrderTotalShouldBe($arg1)
    {
        $total = $this->getTotal();
        assertEquals($total, (float)$arg1);
    }

    /**
     * @Given the :arg1 offer is disabled
     */
    public function theOfferIsDisabled($arg1)
    {
        $code = $this->getOfferCode($arg1);
        if(($offset = array_search($code, $this->enabledOffers)) !== false) {
            array_splice($this->enabledOffers, $offset, 1);
        }
    }

    /**
     * @Then I should not get anything for free
     */
    public function iShouldNotGetAnythingForFree()
    {
        $this->processOrder();
        $order = $this->getOrderXML();
        foreach($order->xpath('/order/products/product') as $product) {
            assertGreaterThan(0, (float) $product['price']);
        }
    }

    /**
     * @Then I should get a :discount% discount on :productLabel
     */
    public function iShouldGetADiscountOn($productLabel, $discount)
    {
        $productBefore = $this->getProduct($productLabel);
        $beforePrice = (float) $productBefore['price'];
        $this->processOrder();
        $productAfter = $this->getProduct($productLabel);
        assertEquals($beforePrice * ($discount / 100), (float) $productAfter['price']);
    }

    /** helper functions ... better to refactor in seperate context as code base grows ...*/

    protected function getOfferCode($label) {
        switch($label) {
            case '3 for the price of 2':
                $code = 'o1';
                break;
            case 'Buy Shampoo & get Conditioner for 50% off':
                $code = 'o2';
                break;
            default:
                throw new \OutOfRangeException(sprintf('Offer "%s" does not exist!', $label));
        }

        return $code;
    }

    protected function saveTmpFile($content, $ext = '.txt') {
        if(!$this->rootFS) {
            $this->rootFS = vfsStream::setup('tmp');
        }

        $filePath = $this->rootFS->url() . '/file-' . microtime(true) . $ext;
        file_put_contents($filePath, $content);
        return $filePath;
    }

    protected function getProduct($title, \SimpleXMLElement $order = null) {
        $order = $order? $order : $this->getOrderXML();

        foreach($order->xpath('/order/products/product') as $product) {
            if((string) $product['title'] === $title) {
                return $product;
            }
        }

        throw new \OutOfRangeException(sprintf('Product not found in order xml: "%s"', $title));
    }

    protected function processOrder() {
        $app = new \App\Console;
        $app->setAutoExit(false);
        $tester = new ApplicationTester($app);

        $options = [
            'command' => 'offers',
            'xml' => $this->orderFile,
        ];

        if(count($this->enabledOffers)) {
            $options['--offer'] = $this->enabledOffers;
        }

        $tester->run($options);

        assertEquals(0, $tester->getStatusCode(), $tester->getDisplay());
    }

    protected function getOrderXML() {
        assertFileExists($this->orderFile);
        $xmlContent = file_get_contents($this->orderFile);
        return new \SimpleXMLElement($xmlContent);
    }

    protected function getTotal(\SimpleXMLElement $order = null) {
        $order = $order? $order : $this->getOrderXML();
        return (float) $order->xpath('/order')[0]->total;
    }
}
