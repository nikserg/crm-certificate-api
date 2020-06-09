<?php

namespace nikserg\CRMCertificateAPI\models\response;


use nikserg\CRMCertificateAPI\models\response\models\Platforms;
use nikserg\CRMCertificateAPI\models\response\models\ProductTemplates;

/**
 * Получения цен по платфомам и продуктам
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class GetPrice
{
    /**
     * Информация о предмет каталоге.
     *
     * @var ProductTemplates[] $productTemplates
     */
    public $productTemplates;

    /**
     * Информация о платформах
     *
     * @var Platforms[] $platforms
     */
    public $platforms;

    /**
     * Информация о не найденных платформах или которые были заблокированы.
     *
     * @var string[] $notFoundPlatforms
     */
    public $notFoundPlatforms;



}