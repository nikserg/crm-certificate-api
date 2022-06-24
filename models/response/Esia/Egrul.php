<?php

namespace nikserg\CRMCertificateAPI\models\response\Esia;

/**
 * Запросы к ЕСИА
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class Egrul
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $status;
    /**
     * @var string
     */
    public $comment;
    public $shortName;
    public $fullName;
    public $ogrn;
    public $ogrnip;
    public $inn;
    public $kpp;
    /**
     * @var Address
     */
    public $address;
    /**
     * @var Person
     */
    public $head;

    /**
     * @param $json
     */
    public function __construct($json)
    {
        $this->id = $json->id;
        $this->status = $json->status;
        $this->comment = $json->comment;
        if (isset($json->data)) {
            $data = $json->data;
            $this->shortName = $data->organizationShortName;
            $this->fullName = $data->organizationFullName ?? null;
            $this->ogrn = $data->OGRN ?? null;
            $this->ogrnip = $data->OGRNIP ?? null;
            $this->inn = $data->INN;
            $this->kpp = $data->KPP ?? null;
            $this->address = new Address();
            $this->address->region = $data->region ?? null;
            $this->address->city = $data->city ?? null;
            $this->address->street = $data->street ?? null;
            $this->address->raw = $data->fiasAddress ?? null;
            $this->head = new Person();
            $this->head->firstName = $data->headFirstName;
            $this->head->middleName = $data->headMiddleName ?? null;
            $this->head->lastName = $data->headLastName;
            $this->head->jobName = $data->headPosition ?? null;
            $this->head->inn = $data->headInn ?? null;
        }
    }
}
