<?php

namespace nikserg\CRMCertificateAPI\models\response\Esia;

use nikserg\CRMCertificateAPI\models\response\Esia;


/**
 * Запросы к ЕСИА
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class GetEgrul extends Esia
{
    protected function parseResponse($rawResponse)
    {
        print_r($rawResponse);exit;
    }
}