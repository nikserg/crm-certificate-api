<?php

namespace nikserg\CRMCertificateAPI\models\response;

use nikserg\CRMCertificateAPI\models\response\models\PartnerPlatform;

/**
 * Структура ответа с платформами партнера
 */
class PartnerPlatforms
{
    /**
     * Платформы доступные клиентам партнера
     *
     * @var PartnerPlatform[]
     */
    public $availablePlatforms;
}