<?php

namespace nikserg\CRMCertificateAPI;
class Client {

    protected $apiKey;
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }
}
