<?php

namespace nikserg\CRMCertificateAPI\models\request;

class Egrul
{
    /**
     * @var string - legal | individual
     */
    public $legalForm;
    /**
     * @var string
     */
    public $inn;
    /**
     * @var int|null - id связанной заявки crm если есть
     */
    public $customerForm;
}