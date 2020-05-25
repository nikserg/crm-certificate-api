<?php

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Запросы к ЕСИА
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
abstract class Esia
{
    public const STATUS_INIT = 0;
    public const STATUS_SENT = 1;
    public const STATUS_FAIL = 2;
    public const STATUS_SUCCESS = 3;
    public const STATUS_EXECUTED = 4;
    public const STATUS_CANCELED = 5;

    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $status;

    /**
     * @var string
     */
    public $rawResponse;

    public function __construct($json)
    {
        $json = json_decode($json, true);
        $this->id = $json['id'];
        $this->status = $json['status'];
        $this->rawResponse = $json['response'];
        if ($this->rawResponse) {
            $this->parseResponse($this->rawResponse);
        }
    }

    abstract protected function parseResponse($rawResponse);
}