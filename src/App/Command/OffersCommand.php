<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OffersCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('offers')
            ->setDescription('Process Order XML File')
            ->addArgument('xml', InputArgument::REQUIRED, 'xml file path')
            ->addOption('offer', 'o', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'list of offer codes to apply to the order', [])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $xmlPath = $input->getArgument('xml');
        $offers = $input->getOption('offer');
        $xmlContent = file_get_contents($xmlPath);
        $orderXML = new \SimpleXMLElement($xmlContent);
        $order = new \App\Processor;
        $order->registerOffer('o1', new \App\Offer\ThreeForTwoOffer);
        $order->registerOffer('o2', new \App\Offer\ShampooGetConditionerHalfPriceOffer);
        $processedOrderXML = $order->process($orderXML, $offers);

        file_put_contents($xmlPath, $processedOrderXML->asXml());
    }
}
