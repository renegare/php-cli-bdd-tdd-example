<?php

namespace App;

use Symfony\Component\Console\Application;

class Console extends Application {
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN') {
        parent::__construct($name, $version);

        $this->loadCommands();
    }

    protected function loadCommands() {
        $this->add(new Command\OffersCommand);
    }
}
