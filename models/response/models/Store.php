<?php

namespace nikserg\CRMCertificateAPI\models\response\models;

/**
 * Описывает склад / точку выдачи
 */
class Store
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $address;
    /**
     * @var string
     */
    public $phone;
    /**
     * @var string (float)
     */
    public $lat;
    /**
     * @var string (float)
     */
    public $lng;
}