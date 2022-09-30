<?php

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Информация об оплате сертификата
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class PaymentInfo
{

    /**
     * @var int
     */
    public $opportunityId;

    /**
     * @var float
     */
    public $totalPrice;

    /**
     * @var string
     */
    public $paymentLink;

    /**
     * @var bool
     */
    public $isPayed;

    /**
     * @var string|null
     */
    public $paymentDate;

}
