<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Продукт в CRM.
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendProductTemplate
{
    /** @var float Цена продукта */
    public $price;
    /** @var int Номер в CRM */
    public $crmId;
}