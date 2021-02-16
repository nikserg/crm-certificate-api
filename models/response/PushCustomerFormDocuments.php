<?php

namespace nikserg\CRMCertificateAPI\models\response;

/**
 * Ответ на отправку документов
 * Каждое поле содержит количество байт
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class PushCustomerFormDocuments
{
    /**
     * Номер заявки
     *
     * @var int
     */
    public $customerFormId;

    /**
     * Подписанное заявление
     *
     * @var int
     */
    public $signedClaim;

    /**
     * Подписанный бланк заявки
     *
     * @var int
     */
    public $signedBlank;

    /**
     * Паспорт, страница с фото
     *
     * @var int
     */
    public $passportphoto;

    /**
     * Заявление на выпуск сертификата
     *
     * @var int
     */
    public $union;

}