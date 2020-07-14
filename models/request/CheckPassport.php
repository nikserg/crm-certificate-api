<?php

namespace nikserg\CRMCertificateAPI\models\request;

class CheckPassport
{
    /**
     * @var string
     */
    public $series;
    /**
     * @var string
     */
    public $number;
    /**
     * @var string - dd.mm.YYYY
     */
    public $issueDate;
    /**
     * @var string
     */
    public $issuer;
    /**
     * @var string
     */
    public $issuerCode;
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
     * @var string - dd.mm.YYYY
     */
    public $birthday;
    /**
     * @var string
     */
    public $birthplace;
    /**
     * @var int - 1: муж. 2: жун.
     */
    public $gender;
}