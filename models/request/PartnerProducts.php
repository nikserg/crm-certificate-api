<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Запрос на получение доступных продуктов партнера
 */
class PartnerProducts
{
    /**
     * Номер пользователя партнера
     *
     * @var int
     */
    public $partnerUserId;
}