<?php

namespace nikserg\CRMCertificateAPI\models\response\Esia;

/**
 * Должностное лицо
 *
 * @package nikserg\CRMCertificateAPI\models\response\Esia
 */
class Person
{
    public $firstName;
    public $lastName;
    public $middleName;
    public $jobName;
    public $inn;

    /**
     * @return string
     */
    public function getFIO()
    {
        return implode(' ', [$this->lastName, $this->firstName, $this->middleName]);
    }
}