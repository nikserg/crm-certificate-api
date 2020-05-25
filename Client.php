<?php

namespace nikserg\CRMCertificateAPI;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use nikserg\CRMCertificateAPI\exceptions\BooleanResponseException;
use nikserg\CRMCertificateAPI\exceptions\NotFoundException;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\Esia\GetEgrul;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use Psr\Http\Message\ResponseInterface;

class Client
{
    public const PRODUCTION_URL = 'https://crm.uc-itcom.ru/index.php/';
    public const TEST_URL = 'https://dev.uc-itcom.ru/index.php/';
    private const ACTION_ADD_CUSTOMER_FORM = 'gateway/itkExchange/pushCustomerForm';
    private const ACTION_GET_CUSTOMER_FORM = 'gateway/itkExchange/pullCustomerForm';
    private const ACTION_DELETE_CUSTOMER_FORM = 'gateway/itkExchange/deleteCustomerForm';
    private const ACTION_GET_OPPORTUNITY = 'gateway/itkExchange/pullOpportunity';
    private const ACTION_UNION = 'gateway/itkExchange/union';
    private const ACTION_CERTIFICATE_BLANK = 'gateway/itkExchange/certificateBlank';
    private const ACTION_CHANGE_STATUS = 'gateway/itkExchange/pushCustomerFormStatus';
    private const ACTION_EGRUL = 'gateway/itkExchange/egrul';
    private const ACTION_PUSH_CUSTOMER_FORM_DATA = 'gateway/itkExchange/pushCustomerFormData';
    protected $apiKey;
    protected $url;
    protected $guzzle;

    /**
     * Client constructor.
     *
     * @param string $apiKey
     * @param string $url
     */
    public function __construct($apiKey, $url = self::PRODUCTION_URL)
    {
        $this->apiKey = $apiKey;
        $this->url = $url;
        $this->guzzle = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws \Exception
     */
    private function getJsonBody(ResponseInterface $response)
    {
        $body = $response->getBody();
        if (!$body) {
            throw new  \Exception('Получено пустое тело ответа на запрос');
        }
        $json = @json_decode($body);
        if (!$json) {
            throw new  \Exception('Невозможно распарсить ответ ' . print_r($body, true));
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
        $url = $this->url . self::ACTION_ADD_CUSTOMER_FORM . '?key=' . $this->apiKey;
        $result = $this->guzzle->post($url, [RequestOptions::JSON => $customerForm]);
        $result = $this->getJsonBody($result);
        $response = new SendCustomerFormResponse();
        $response->id = $result->id;
        $response->token = $result->token;
        $response->generationToken = $result->generationToken;
        return $response;
    }

    /**
     * Получить информацию о заявке на сертификат
     *
     *
     * @param int $customerFormCrmId
     * @return GetCustomerForm
     */
    public function getCustomerForm($customerFormCrmId)
    {
        $url = $this->url . self::ACTION_GET_CUSTOMER_FORM . '?key=' . $this->apiKey;
        $result = $this->guzzle->get($url . '&id=' . $customerFormCrmId);
        $result = $this->getJsonBody($result);
        $response = new GetCustomerForm();
        $response->status = $result->status;
        $response->tokenCertificate = $result->token ?? '';
        $response->opportunityId = $result->opportunityId ?? '';
        return $response;
    }

    /**
     * Получить информацию о сделки
     *
     * @param $opportunityCrmId
     * @return GetOpportunity
     * @throws \Exception
     */
    public function getOpportunity($opportunityCrmId)
    {
        $url = $this->url . self::ACTION_GET_OPPORTUNITY . '?key=' . $this->apiKey;
        $result = $this->guzzle->get($url . '&id=' . $opportunityCrmId);
        $result = $this->getJsonBody($result);
        $response = new GetOpportunity();
        $response->isPay = $result->isPay ?? '';
        $response->accountId = $result->accountId ?? '';
        $response->paymentToken = $result->paymentToken ?? '';
        return $response;
    }

    /**
     * Изменить статус заявки
     *
     *
     * @param ChangeStatus $changeStatus
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws GuzzleException
     * @throws NotFoundException
     */
    public function changeStatus(ChangeStatus $changeStatus)
    {
        $url = $this->url . self::ACTION_CHANGE_STATUS . '?key=' . $this->apiKey;
        try {
            $result = $this->guzzle->post($url, [RequestOptions::JSON => $changeStatus]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $changeStatus->id);
            }
            throw $e;
        }
        $result = $this->getJsonBody($result);
        $response = new BooleanResponse();
        $response->status = $result->status;
        if (property_exists($result, 'message')) {
            $response->message = $result->message;
        }
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при обновлении статуса в CRM ' . print_r($response, true));
        }
        return $response;
    }

    /**
     * Удалить заявку на сертификат
     *
     *
     * @param int $customerFormCrmId
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws NotFoundException
     * @throws GuzzleException
     */
    public function deleteCustomerForm($customerFormCrmId)
    {
        $url = $this->url . self::ACTION_DELETE_CUSTOMER_FORM . '?key=' . $this->apiKey;
        try {
            $result = $this->guzzle->get($url . '&id=' . $customerFormCrmId);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        $result = $this->getJsonBody($result);
        $response = new BooleanResponse();
        $response->status = $result->status;
        if (property_exists($result, 'message')) {
            $response->message = $result->message;
        }
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при удалении заявки в CRM ' . print_r($response, true));
        }
        return $response;
    }

    /**
     * Получить заявление на выпуск сертификата
     *
     * @param int    $customerFormCrmId
     * @param string $format
     * @return string
     * @throws NotFoundException
     * @throws GuzzleException
     */
    public function getCustomerFormClaim($customerFormCrmId, $format = 'pdf')
    {
        $url = $this->url . self::ACTION_UNION . '?key=' . $this->apiKey;
        try {
            $result = $this->guzzle->get($url . '&id=' . $customerFormCrmId . '&format=' . $format);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        return $result->getBody()->getContents();
    }

    /**
     * Получить заявление на выпуск сертификата
     *
     * @param int    $customerFormCrmId
     * @param string $format
     * @return string
     * @throws NotFoundException
     * @throws GuzzleException
     */
    public function getCustomerFormCertificateBlank($customerFormCrmId, $format = 'pdf')
    {
        try {
            $result = $this->guzzle->get($this->url . self::ACTION_CERTIFICATE_BLANK, [
                'query' => [
                    'key'    => $this->apiKey,
                    'id'     => $customerFormCrmId,
                    'format' => $format,
                ],
            ]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        return $result->getBody()->getContents();
    }

    public function getEgrul($customerFormCrmId)
    {
        try {
            $result = $this->guzzle->get($this->url . self::ACTION_EGRUL, [
                'query' => [
                    'key'            => $this->apiKey,
                    'customerFormId' => $customerFormCrmId,
                ],
            ]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        $json = $result->getBody()->getContents();
        return new GetEgrul($json);
    }

    /**
     * Индивидуальная ссылка для редактирования
     *
     *
     * @param $token
     * @return string
     */
    public function editUrl($token)
    {
        return $this->url . 'customerForms/external?token=' . $token;
    }

    /**
     * Индивидуальная ссылка для генерации
     *
     *
     * @param      $token
     * @param      $generatonToken
     * @param bool $iframe Выводить отображение для фрейма
     * @return string
     */
    public function generationUrl($token, $generatonToken, $iframe = false)
    {
        $return = $this->url . 'customerForms/external/generate?token=' . $token . '&generationToken=' . $generatonToken;
        if ($iframe) {
            $return .= '&iframe=1';
        }
        return $return;
    }

    /**
     * Отправить данные бланка заявки на сертификат
     *
     * @param SendCustomerFormData $customerForm
     * @return SendCustomerFormResponse
     * @throws \Exception
     */
    public function sendCustomerFormData(SendCustomerFormData $customerForm)
    {
        $result = $this->guzzle->post($this->url . self::ACTION_PUSH_CUSTOMER_FORM_DATA, [
            RequestOptions::QUERY => ['key' => $this->apiKey],
            RequestOptions::JSON  => [
                'id' => $customerForm->id,
                'formData' => $customerForm->formData
            ],
        ]);
        $result = $this->getJsonBody($result);
        $response = new SendCustomerFormResponse();
        $response->id = $result->id;
        $response->token = $result->token;
        $response->generationToken = $result->generationToken;
        return $response;
    }
}
