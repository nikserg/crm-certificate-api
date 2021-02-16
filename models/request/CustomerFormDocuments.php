<?php

namespace nikserg\CRMCertificateAPI\models\request;


class CustomerFormDocuments
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
     * @var string
     */
    public $signedClaim;

    /**
     * Подписанный бланк заявки
     *
     * @var string
     */
    public $signedBlank;

    /**
     * Паспорт, страница с фото
     *
     * @var string
     */
    public $passportphotoPath;

    /**
     * Заявление на выпуск сертификата
     *
     * @var string
     */
    public $unionPath;
}