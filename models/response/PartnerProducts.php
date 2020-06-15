<?php

namespace nikserg\CRMCertificateAPI\models\response;

use nikserg\CRMCertificateAPI\models\response\models\PartnerProduct;

/**
 * Структура ответа с настроенными продуктами партнера
 */
class PartnerProducts
{
    /**
     * Флаг - у партнера есть настройки продуктов
     *
     * @var bool
     */
    public $hasSettings;
    /**
     * Платформы доступные клиентам партнера
     *
     * @var PartnerProduct[]
     */
    public $availableProducts;
}