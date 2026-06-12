<?php

namespace App\Services\Webserver;

use App\Models\Domain;

interface WebserverInterface
{
    public function generateConfig(Domain $domain): string;

    public function getConfigPath(Domain $domain): string;
}
