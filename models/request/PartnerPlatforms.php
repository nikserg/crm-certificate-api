<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Запрос на получение доступных платформ
 */
class PartnerPlatforms
{
    /**
     * Номер пользователя партнера
     *
     * @var int
     */
    public $partnerUserId;

    /**
     * Организационно-правовая форма клиента
     *
     * @var string
     */
    public $clientLegalForm;
}