<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Запрос на определения такой же подписи
 */
class DetectPlatforms
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

    /**
     * Криптопровайдер клиента
     *
     * @var string
     */
    public $cryptoProvider;

    /**
     * Срок действия подписи в месяцах
     *
     * @var int
     */
    public $period;

    /**
     * Список ОИДов, для которых определяем платформу
     *
     * @var string[]
     */
    public $oids;

    /**
     * Ограничивающий список платформ
     *
     * @var string[]
     */
    public $platforms;
}