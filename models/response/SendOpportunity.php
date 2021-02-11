<?php

namespace nikserg\CRMCertificateAPI\models\response;

/**
 * Class SendOpportunity
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class SendOpportunity
{
    /**
     * ID сделки
     *
     * @var int
     */
    public $zurmoid;

    public $bitrixid;

    public $applicationid;

    /**
     * Ссылка на сделку
     *
     * @var string
     */
    public $link;

    /**
     * Токен для оплаты
     *
     *
     * @var string
     */
    public $paytoken;
}