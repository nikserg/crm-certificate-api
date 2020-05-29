<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Отправка реферал ссылки
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendCheckRef
{
    /**
     * Реферал. содержащий наименования пользователя.
     *
     * @var string $referral
     */
    public $referral;
}