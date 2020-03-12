<?php

namespace nikserg\CRMCertificateAPI;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use nikserg\CRMCertificateAPI\models\out\CustomerForm;
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
        $this->guzzle = new \GuzzleHttp\Client();
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
     * @param CustomerForm $customerForm
     * @return int ID заявки в CRM
     */
    public function sendCustomerForm(CustomerForm $customerForm)
    {
        $url = $this->url . self::ACTION_ADD_CUSTOMER_FORM;

        $postData = [
            'form_params' => (array)$customerForm,
        ];
        print_r($postData);
        exit;
        $result = $this->guzzle->post($url, $postData);
        $result = $this->getJsonBody($result);
        return $result['zurmo-id'];
    }
}
