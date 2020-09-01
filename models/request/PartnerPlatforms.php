<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Запрос на получение доступных платформ партнера
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
     * Организационно-правовая форма клиента. По умолчанию 'legal'
     *
     * @var string
     */
    public $clientLegalForm;

    /**
     * Криптопровайдер клиента
     *
     * @var string
     */
    public $cryptoProvider;

    /**
     * Период действие сертфиката. По умолчанию берется 1 год
     *
     * @var string
     */
    public $period;

    /**
     * Выбранные платформы (разбитые)
     *
     * @var string[]
     */
    public $selectedPlatforms = [];
}