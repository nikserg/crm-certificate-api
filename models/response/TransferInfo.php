<?php

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Информация о трансфере заявки
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class TransferInfo
{
    /**
     * ID склада
     *
     * @var ?int
     */
    public $id;

    /**
     * Город
     *
     * @var ?string
     */
    public $city;
    /**
     * Адрес
     *
     * @var ?string
     */
    public $address;
}
