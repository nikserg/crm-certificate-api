<?php

namespace nikserg\CRMCertificateAPI;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use Psr\Http\Message\ResponseInterface;

class Client
{

    public const PRODUCTION_URL = 'https://crm.uc-itcom.ru/index.php/';
    public const TEST_URL = 'https://dev.uc-itcom.ru/index.php/';

    private const ACTION_ADD_CUSTOMER_FORM = 'gateway/itkExchange/pushCustomerForm';

    protected $apiKey;
    protected $url;
    protected $guzzle;

    public function __construct($apiKey, $url = self::PRODUCTION_URL)
    {
        $this->apiKey = $apiKey;
        $this->url = $url;
        $this->guzzle = new \GuzzleHttp\Client([
            'verify' => false]);

    }

    private function getJsonBody(ResponseInterface $response) {
        $body = $response->getBody();
        if (!$body) {
            throw new  \Exception('Получено пустое тело ответа на запрос');
        }
        $json = @json_decode($body);
        if (!$json) {
            throw new  \Exception('Невозможно распарсить ответ '.print_r($body, true));
        }
        return $json;
    }

    /**
     * Отправить запрос на создание заявки на сертификат
     *
     *
     * @param SendCustomerFormRequest $customerForm
     * @return SendCustomerFormResponse
     */
    public function sendCustomerForm(SendCustomerFormRequest $customerForm)
    {
        $url = $this->url . self::ACTION_ADD_CUSTOMER_FORM.'?key='.$this->apiKey;

        $result = $this->guzzle->post($url, [RequestOptions::JSON => $customerForm]);
        $result = $this->getJsonBody($result);

        $response = new SendCustomerFormResponse();
        $response->id = $result->id;
        $response->token = $result->token;
        $response->generationToken = $result->generationToken;
        return $response;
    }
}
