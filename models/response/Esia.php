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
    protected $rawResponse;

    /**
     * Esia constructor.
     *
     * @param array $json
     */
    public function __construct($json)
    {
        $this->id = $json['id'];
        $this->status = $json['status'];
        $this->rawResponse = $json['response'];
        if ($this->rawResponse) {
            $this->parseResponse($this->rawResponse);
        }
    }

    /**
     * Завершен ли запрос
     *
     *
     * @return bool
     */
    public function isDone()
    {
        return $this->status >= self::STATUS_EXECUTED;
    }

    abstract protected function parseResponse($rawResponse);
}