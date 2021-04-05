<?php

namespace App\Holiday;

use Illuminate\Support\Facades\Http;

class Client
{
    public $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function request($countryCode, $year)
    {
        $uri = "?jahr=$year&nur_land=$countryCode";

        $response = Http::get($this->config['base_url'].$uri);

        if ($response->failed()) {
            throw new \Exception(__("Sth. went wrong."));
        }

        return new Holidays($response->json());
    }
}
