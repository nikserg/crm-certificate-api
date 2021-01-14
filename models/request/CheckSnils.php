<?php

namespace nikserg\CRMCertificateAPI\models\request;

class CheckSnils
{
    /**
     * @var string `000-000-000 00`
     */
    public $snils;
    /**
     * @var string `dd.mm.YYYY`
     */
    public $birthDate;
    /**
     * @var string
     */
    public $firstName;
    /**
     * @var string
     */
    public $familyName;
    /**
     * @var string|null
     */
    public $patronymic;
    /**
     * @var string - Female|Male
     */
    public $gender;
    /**
     * @var int|null - id связанной заявки crm если есть
     */
    public $customerForm;
}