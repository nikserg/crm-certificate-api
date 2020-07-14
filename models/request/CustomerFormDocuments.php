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
}