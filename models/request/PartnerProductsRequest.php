<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Запрос на получение доступных продуктов партнера
 */
class PartnerProductsRequest
{
    /**
     * Номер пользователя партнера
     *
     * @var int
     */
    public $partnerUserId;
}