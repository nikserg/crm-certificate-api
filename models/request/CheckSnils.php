<?php

namespace nikserg\CRMCertificateAPI\models\request;

class CheckSnils
{
    /**
     * @var string
     */
    public $snils;
    /**
     * @var string - dd.mm.YYYY
     */
    public $birthDate;
    /**
     * @var string
     */
    public $firstName;
    /**
     * @var string
     */
    public $lastName;
    /**
     * @var string
     */
    public $middleName;
    /**
     * @var int - 1: муж. 2: жун.
     */
    public $gender;
    /**
     * @var int|null - id связанной заявки crm если есть
     */
    public $customerForm;
}