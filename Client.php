<?php

namespace nikserg\CRMCertificateAPI;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use nikserg\CRMCertificateAPI\exceptions\BooleanResponseException;
use nikserg\CRMCertificateAPI\exceptions\InvalidRequestException;
use nikserg\CRMCertificateAPI\exceptions\NotFoundException;
use nikserg\CRMCertificateAPI\exceptions\ServerException;
use nikserg\CRMCertificateAPI\exceptions\TransportException;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\CheckPassport;
use nikserg\CRMCertificateAPI\models\request\CheckSnils;
use nikserg\CRMCertificateAPI\models\request\CustomerFormDocuments;
use nikserg\CRMCertificateAPI\models\request\DetectPlatforms as DetectPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\Egrul as EgrulRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerFullPrice as PartnerFullPriceRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerPlatforms as PartnerPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerProducts as PartnerProductsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerStores as PartnerStoresRequest;
use nikserg\CRMCertificateAPI\models\request\SendCheckRef;
use nikserg\CRMCertificateAPI\models\request\SendCrtFile;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\request\SendOpportunity as SendOpportunityRequest;
use nikserg\CRMCertificateAPI\models\request\SendReqFile;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\DetectPlatformVariant;
use nikserg\CRMCertificateAPI\models\response\Esia\Egrul as EgrulResponse;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\models\DetectPlatformVariantPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerProduct;
use nikserg\CRMCertificateAPI\models\response\models\Store;
use nikserg\CRMCertificateAPI\models\response\PassportCheck;
use nikserg\CRMCertificateAPI\models\response\PushCustomerFormDocuments;
use nikserg\CRMCertificateAPI\models\response\ReferralUser;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use nikserg\CRMCertificateAPI\models\response\SendOpportunity as SendOpportunityResponse;
use nikserg\CRMCertificateAPI\models\response\SnilsCheck;
use Psr\Http\Message\ResponseInterface;

/**
 * Клиент для связи с CRM
 *
 * @package nikserg\CRMCertificateAPI
 */
class Client
{
    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * @param string $apiKey
     * @param string $url
     */
    public function __construct(string $apiKey, string $url = 'https://crm.uc-itcom.ru/index.php')
    {
        $this->apiKey = $apiKey;
        $this->url = trim($url, " /");
        $this->guzzle = new \GuzzleHttp\Client([
            RequestOptions::VERIFY      => false,
            RequestOptions::HTTP_ERRORS => false,
        ]);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array  $options
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     * @throws InvalidRequestException
     */
    protected function request(string $method, string $endpoint, array $options = []): ResponseInterface
    {
        $options[RequestOptions::QUERY]['key'] = $this->apiKey;
        try {
            $response = $this->guzzle->request($method, "$this->url/gateway/itkExchange/$endpoint", $options);
        } catch (GuzzleException $e) {
            throw new TransportException("Ошибка запроса; {$e->getMessage()}");
        }
        switch ($response->getStatusCode()) {
            case 200:
            case 204:
                return $response;
            case 400:
                throw new InvalidRequestException("$endpoint: Неверный формат запроса");
            case 404:
                throw new NotFoundException("$endpoint: Сущность или точка АПИ не найдены: \n " . $response->getBody()->getContents());
            case 500:
                throw new ServerException("$endpoint: Ошибка сервера \n" . $response->getBody()->getContents());
            default:
                throw new TransportException("$endpoint: Неожиданный код ответа {$response->getStatusCode()}");
        }
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param mixed  $data
     * @param array  $options
     * @return mixed
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     * @throws InvalidRequestException
     */
    protected function requestJson(string $method, string $endpoint, $data, array $options = [])
    {
        $options[RequestOptions::QUERY]['key'] = $this->apiKey;
        $options[RequestOptions::JSON] = $data;
        try {
            $response = $this->guzzle->request($method, "$this->url/gateway/itkExchange/$endpoint", $options);
        } catch (GuzzleException $e) {
            throw new TransportException("Ошибка запроса; {$e->getMessage()}");
        }
        try {
            $data = $this->getJsonBody($response);
            switch ($response->getStatusCode()) {
                case 200:
                    return $data;
                case 400:
                    throw new InvalidRequestException("$endpoint: " . $data->error->message ?? $data->message ?? "Неверный формат запроса");
                case 404:
                    throw new NotFoundException("$endpoint: " . $data->error->message ?? $data->message ?? "Сущность или точка АПИ не найдены");
                case 500:
                    throw new ServerException("$endpoint: " . $data->error->message ?? $data->message ?? "Неожиданная ошибка сервера");
                default:
                    throw new TransportException("$endpoint: " . $data->error->message ?? $data->message ?? "Неожиданный код ответа {$response->getStatusCode()}");
            }
        } catch (TransportException $e) {
            throw new TransportException('Ошибка во время отправки запроса ' . print_r([
                    $method,
                    $this->url . $endpoint,
                    $options,
                    $data,
                ], true) . ': ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws TransportException
     */
    private function getJsonBody(ResponseInterface $response)
    {
        $body = $response->getBody();
        if (strlen($body) === 0) {
            throw new TransportException('Пустое тело ответа на JSON запрос. Код ответа ' . $response->getStatusCode());
        }
        $json = json_decode($body);
        $jsonErrorCode = json_last_error();
        $jsonErrorMessage = json_last_error_msg();
        if ($jsonErrorCode !== JSON_ERROR_NONE) {
            throw new TransportException("$jsonErrorMessage: $body", $jsonErrorCode);
        }

        return $json;
    }

    /**
     * Отправить запрос на создание заявки на сертификат
     *
     * @param SendCustomerFormRequest $customerForm
     * @return SendCustomerFormResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function sendCustomerForm(SendCustomerFormRequest $customerForm): SendCustomerFormResponse
    {
        $result = $this->requestJson('POST', 'pushCustomerForm', $customerForm);

        return $this->fill(SendCustomerFormResponse::class, $result);
    }

    /**
     * Отправить запрос на создание/изменение сделки и создание/изменение клиента
     *
     *
     * @param SendOpportunityRequest $sendOpportunity
     * @return SendOpportunityResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function sendOpportunity(SendOpportunityRequest $sendOpportunity): SendOpportunityResponse
    {

        $result = $this->requestJson('POST', 'pushOpportunity', $sendOpportunity);

        return $this->fill(SendOpportunityResponse::class, $result);
    }

    /**
     * Получить информацию о заявке на сертификат
     *
     * @param int $customerFormCrmId
     * @return GetCustomerForm
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getCustomerForm(int $customerFormCrmId): GetCustomerForm
    {
        $result = $this->getJsonBody($this->request('GET', 'pullCustomerForm', [
            RequestOptions::QUERY => [
                'id' => $customerFormCrmId,
            ],
        ]));

        return $this->fill(GetCustomerForm::class, $result);
    }

    /**
     * Получить информацию о сделке
     *
     * @param int $opportunityCrmId
     * @return GetOpportunity
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getOpportunity(int $opportunityCrmId): GetOpportunity
    {
        $result = $this->getJsonBody($this->request('GET', 'pullOpportunity', [
            RequestOptions::QUERY => [
                'id' => $opportunityCrmId,
            ],
        ]));

        return $this->fill(GetOpportunity::class, $result);
    }

    /**
     * Изменить статус заявки
     *
     * @param ChangeStatus $changeStatus
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function changeStatus(ChangeStatus $changeStatus): BooleanResponse
    {
        $result = $this->requestJson('POST', 'pushCustomerFormStatus', $changeStatus);
        $response = new BooleanResponse();
        $response->status = $result->status;
        $response->message = $result->message ?? null;
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при обновлении статуса в CRM ' . print_r($response, true));
        }

        return $response;
    }


    /**
     * Отправить файл запроса
     *
     *
     * @param \nikserg\CRMCertificateAPI\models\request\SendReqFile $sendReqFile
     * @return \nikserg\CRMCertificateAPI\models\response\BooleanResponse
     * @throws \nikserg\CRMCertificateAPI\exceptions\BooleanResponseException
     * @throws \nikserg\CRMCertificateAPI\exceptions\InvalidRequestException
     * @throws \nikserg\CRMCertificateAPI\exceptions\NotFoundException
     * @throws \nikserg\CRMCertificateAPI\exceptions\ServerException
     * @throws \nikserg\CRMCertificateAPI\exceptions\TransportException
     */
    public function sendReqFile(SendReqFile $sendReqFile): BooleanResponse
    {
        $result = $this->requestJson('POST', 'pushCustomerFormReqFile', $sendReqFile);
        $response = new BooleanResponse();
        $response->status = $result->status;
        $response->message = $result->message ?? null;
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при отправке файла запроса в CRM ' . print_r($response, true));
        }

        return $response;
    }


    /**
     * Отправить файл выпущенного сертификата
     *
     *
     * @param \nikserg\CRMCertificateAPI\models\request\SendCrtFile $sendCrtFile
     * @return \nikserg\CRMCertificateAPI\models\response\BooleanResponse
     * @throws \nikserg\CRMCertificateAPI\exceptions\BooleanResponseException
     * @throws \nikserg\CRMCertificateAPI\exceptions\InvalidRequestException
     * @throws \nikserg\CRMCertificateAPI\exceptions\NotFoundException
     * @throws \nikserg\CRMCertificateAPI\exceptions\ServerException
     * @throws \nikserg\CRMCertificateAPI\exceptions\TransportException
     */
    public function sendCrtFile(SendCrtFile $sendCrtFile): BooleanResponse
    {
        $result = $this->requestJson('POST', 'pushCustomerFormCrtFile', $sendCrtFile);
        $response = new BooleanResponse();
        $response->status = $result->status;
        $response->message = $result->message ?? null;
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при отправке файла выпущенного сертификата в CRM ' . print_r($response,
                    true));
        }

        return $response;
    }

    /**
     * Удалить заявку на сертификат
     *
     * @param int $customerFormCrmId
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function deleteCustomerForm(int $customerFormCrmId): BooleanResponse
    {
        $result = $this->getJsonBody($this->request('GET', 'deleteCustomerForm', [
            RequestOptions::QUERY => [
                'id' => $customerFormCrmId,
            ],
        ]));
        $response = new BooleanResponse();
        $response->status = $result->status;
        $response->message = $result->message ?? null;
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
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getCustomerFormClaim(int $customerFormCrmId, string $format = 'pdf'): string
    {
        $result = $this->request('GET', 'union', [
            RequestOptions::QUERY => [
                'id'     => $customerFormCrmId,
                'format' => $format,
            ],
        ]);

        return $result->getBody()->getContents();
    }


    /**
     * Откатить заявку на нулевой статус
     *
     *
     * @param int $customerFormCrmId
     * @return \nikserg\CRMCertificateAPI\models\response\BooleanResponse
     * @throws \nikserg\CRMCertificateAPI\exceptions\BooleanResponseException
     * @throws \nikserg\CRMCertificateAPI\exceptions\InvalidRequestException
     * @throws \nikserg\CRMCertificateAPI\exceptions\NotFoundException
     * @throws \nikserg\CRMCertificateAPI\exceptions\ServerException
     * @throws \nikserg\CRMCertificateAPI\exceptions\TransportException
     */
    public function revert(int $customerFormCrmId): BooleanResponse
    {
        $result = $this->request('GET', 'revert', [
            RequestOptions::QUERY => [
                'id' => $customerFormCrmId,
            ],
        ]);
        $response = new BooleanResponse();
        $response->status = $result->status;
        $response->message = $result->message ?? null;
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при откате заявки в CRM ' . print_r($response, true));
        }

        return $response;
    }

    /**
     * Получить заявление на выпуск сертификата
     *
     * @param int    $customerFormCrmId
     * @param string $format
     * @return string
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getCustomerFormCertificateBlank(int $customerFormCrmId, string $format = 'pdf'): string
    {
        $result = $this->request('GET', 'certificateBlank', [
            RequestOptions::QUERY => [
                'id'     => $customerFormCrmId,
                'format' => $format,
            ],
        ]);

        return $result->getBody()->getContents();
    }

    /**
     * Выписка ЕГРЮЛ
     *
     * @param EgrulRequest $request
     * @return EgrulResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function egrul(EgrulRequest $request): EgrulResponse
    {
        return new EgrulResponse($this->requestJson('GET', 'egrul', $request));
    }

    /**
     * Отправить данные бланка заявки на сертификат
     *
     * @param int                  $crmCustomerFormId
     * @param SendCustomerFormData $customerFormData
     * @return SendCustomerFormResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function sendCustomerFormData(
        int $crmCustomerFormId,
        SendCustomerFormData $customerFormData
    ): SendCustomerFormResponse {
        $result = $this->requestJson('POST', 'pushCustomerFormData', [
            'id'       => $crmCustomerFormId,
            'formData' => $customerFormData,
        ]);

        return $this->fill(SendCustomerFormResponse::class, $result);
    }

    /**
     * Обычная проверка паспортных данных
     *
     * @param CheckPassport $request
     * @return PassportCheck
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function checkPassport(CheckPassport $request): PassportCheck
    {
        $result = $this->requestJson('GET', 'checkPassport', $request);

        return $this->fill(PassportCheck::class, $result);
    }

    /**
     * Расширенная проверка паспортных данных
     *
     *
     * @param \nikserg\CRMCertificateAPI\models\request\CheckPassport $request
     * @return mixed
     * @throws \nikserg\CRMCertificateAPI\exceptions\InvalidRequestException
     * @throws \nikserg\CRMCertificateAPI\exceptions\NotFoundException
     * @throws \nikserg\CRMCertificateAPI\exceptions\ServerException
     * @throws \nikserg\CRMCertificateAPI\exceptions\TransportException
     */
    public function checkPassportExtended(CheckPassport $request)
    {

        $result = $this->requestJson('GET', 'checkPassportExtended', $request);

        return $this->fill(PassportCheck::class, $result);
    }

    /**
     * Проверка СНИЛС данных
     *
     * @param CheckSnils $request
     * @return SnilsCheck
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function checkSnils(CheckSnils $request): SnilsCheck
    {
        $result = $this->requestJson('GET', 'checkSnils', $request);

        return $this->fill(SnilsCheck::class, $result);
    }

    /**
     * Получение информации о реферальном пользователе
     *
     * @param SendCheckRef $sendCheckRef
     * @return ReferralUser|null - null если реферальный пользователь не найден
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getReferralUser(SendCheckRef $sendCheckRef): ?ReferralUser
    {
        $result = $this->requestJson('POST', 'getRefUserInfo', $sendCheckRef);
        if ($result === null) {
            return null;
        }

        return $this->fill(ReferralUser::class, $result);
    }

    /**
     * Определить варианты площадок для переданного списка ОИДов
     *
     * @param DetectPlatformsRequest $request
     * @return DetectPlatformVariant[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function detectPlatforms(DetectPlatformsRequest $request): array
    {
        $result = $this->requestJson('POST', 'detectPlatforms', $request);
        if (empty($result->variants)) {
            return [];
        }
        $return = [];
        foreach ($result->variants as $variant) {
            $variantModel = new DetectPlatformVariant();
            $variantModel->platforms = [];
            foreach ($variant->platforms as $value => $name) {
                $platform = new DetectPlatformVariantPlatform();
                $platform->value = $value;
                $platform->name = $name;
                $variantModel->platforms[] = $platform;
            }
            $variantModel->price = $variant->price;
            $variantModel->excluded = $variant->excluded;
            $return[] = $variantModel;
        }

        return $return;
    }

    /**
     * Отправить файл в CRM
     *
     * @param int    $customerFormId
     * @param string $documentId
     * @param string $fileExt
     * @param mixed  $content
     * @return void
     * @throws \nikserg\CRMCertificateAPI\exceptions\BooleanResponseException
     * @throws \nikserg\CRMCertificateAPI\exceptions\InvalidRequestException
     * @throws \nikserg\CRMCertificateAPI\exceptions\NotFoundException
     * @throws \nikserg\CRMCertificateAPI\exceptions\ServerException
     * @throws \nikserg\CRMCertificateAPI\exceptions\TransportException
     */
    public function pushDocument(int $customerFormId, string $documentId, string $fileExt, mixed $content): void
    {
        $response = $this->request('POST', 'pushCustomerFormDocument', [
            RequestOptions::QUERY     => [
                'id'         => $customerFormId,
                'documentId' => $documentId,
            ],
            RequestOptions::MULTIPART => [
                $documentId => [
                    'name'     => $documentId,
                    'filename' => $documentId . '.' . $fileExt,
                    'contents' => $content,
                ],
            ],
        ]);
        $response = json_decode($response->getBody()->getContents(), true);
        if (!isset($response['code']) || $response['code'] != 0) {
            throw new BooleanResponseException(print_r($response, true));
        }
    }

    /**
     * @param CustomerFormDocuments $documents
     * @return PushCustomerFormDocuments
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function pushCustomerFormDocuments(CustomerFormDocuments $documents): PushCustomerFormDocuments
    {
        $multipart = [
            [
                'name'     => 'customerFormId',
                'contents' => $documents->customerFormId,
            ],
        ];
        if ($documents->signedClaim) {
            $multipart[] = [
                'name'     => 'signedClaim',
                'filename' => 'claim.pdf.sig',
                'contents' => $documents->signedClaim,
            ];
        }
        if ($documents->signedBlank) {
            $multipart[] = [
                'name'     => 'signedBlank',
                'filename' => 'blank.pdf.sig',
                'contents' => $documents->signedBlank,
            ];
        }

        foreach (['union', 'passportphoto', 'foreignerpassport'] as $documentName) {
            $path = $documents->{$documentName . 'Path'};
            if (!file_exists($path)) {
                continue;
            }
            $multipart[] = [
                'name'     => $documentName,
                'filename' => basename($documents->{$documentName . 'Path'}),
                'contents' => file_get_contents($path),
            ];
        }

        $response = $this->request('POST', 'pushCustomerFormDocuments', [
            RequestOptions::MULTIPART => $multipart,
        ]);
        $response = json_decode($response->getBody()->getContents(), true);

        return $this->fill(PushCustomerFormDocuments::class, $response);
    }

    /**
     * Получает платформы, доступные партнеру переданному в запросе
     *
     * @param PartnerPlatformsRequest $request
     * @return PartnerPlatform[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerPlatformsAll(PartnerPlatformsRequest $request): array
    {
        $result = $this->requestJson('POST', 'getPartnerPlatforms', $request);

        return $this->fillList(PartnerPlatform::class, $result->platforms);
    }

    /**
     * Получает продукты, настроенные для партнера переданного в запросе
     *
     * @param PartnerProductsRequest $request
     * @return PartnerProduct[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerProductsAll(PartnerProductsRequest $request): array
    {
        $result = $this->requestJson('POST', 'getPartnerProducts', $request);

        return $this->fillList(PartnerProduct::class, $result->products);
    }

    /**
     * Отдает сумму по выбранным продуктам и плаформам для партнера
     *
     * @param PartnerFullPriceRequest $fullPriceRequest
     * @return float
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerFullPrice(PartnerFullPriceRequest $fullPriceRequest): float
    {
        $result = $this->requestJson('POST', 'getPartnerFullPrice', $fullPriceRequest);

        return $result->price;
    }

    /**
     * Отдает список точек/складов/трансферов партнера
     *
     * @param PartnerStoresRequest $partnerStores
     * @return Store[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerStores(PartnerStoresRequest $partnerStores): array
    {
        $result = $this->requestJson('POST', 'getPartnerStores', $partnerStores);

        return $this->fillList(Store::class, $result->stores);
    }

    /**
     * @param $class
     * @param $attributes
     * @return mixed
     */
    public function fill($class, $attributes)
    {
        $model = new $class;
        foreach ($attributes as $attribute => $value) {
            //zurmo-id => zurmoid
            $attribute = str_replace('-', '', $attribute);
            $model->$attribute = $value;
        }

        return $model;
    }

    public function fillList($class, $list): array
    {
        $models = [];
        foreach ($list as $item) {
            $models[] = $this->fill($class, $item);
        }

        return $models;
    }

    #region urls

    /**
     * Ссылка для скачивания сертификата
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function certificateDownloadUrl($customerFormId, $token): string
    {
        return $this->url . '/customerForms/external/downloadCertificate?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Ссылка для скачивания реализации
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function realizationDownloadUrl($customerFormId, $token): string
    {
        return $this->url . '/customerForms/external/downloadFirstRealization?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Индивидуальная ссылка для редактирования
     *
     * @param $token
     * @return string
     */
    public function editUrl($token): string
    {
        return $this->url . '/customerForms/external?token=' . $token;
    }

    /**
     * Индивидуальная ссылка для генерации
     *
     * @param      $token
     * @param      $generatonToken
     * @param bool $iframe Выводить отображение для фрейма
     * @return string
     */
    public function generationUrl($token, $generatonToken, $iframe = false): string
    {
        $return = $this->url . '/customerForms/external/generate?token=' . $token . '&generationToken=' . $generatonToken;
        if ($iframe) {
            $return .= '&iframe=1';
        }

        return $return;
    }

    public function prerequestUrl($token, $iframe = false): string
    {

        $return = $this->url . '/customerForms/external/prerequest?token=' . $token;
        if ($iframe) {
            $return .= '&iframe=1';
        }

        return $return;
    }

    /**
     * Ссылка на фрейм
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function customerFormFrameUrl($customerFormId, $token): string
    {
        return $this->url . '/customerForms/external/step1?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Ссылка для записи сертификата на носитель
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function certificateWriteUrl($customerFormId, $token): string
    {
        return $this->url . '/customerForms/external/writeCertificate?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Ссылка для оплаты счета онлайн
     *
     *
     * @param string $paymentToken
     * @param bool   $iframe
     * @param string $locale
     * @return string
     */
    public function paymentUrl(string $paymentToken, bool $iframe = false, string $locale = 'ru'): string
    {
        return $this->url . '/clients/payment?paymentToken=' . $paymentToken . '&iframe=' . intval($iframe) . '&locale=' . $locale;
    }

    /**
     * Бланк сертификата
     *
     * @param $customerFormId
     * @param $token
     *
     * @return string
     */
    public function uploadCertificateBlank($customerFormId, $token): string
    {
        return $this->url . '/customerForms/external/uploadCertificateBlank?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Бланк отзыва сертификата
     *
     * @param $customerFormId
     * @param $token
     *
     * @return string
     */
    public function uploadRevocationCertificateBlank($customerFormId, $token): string
    {
        return $this->url . '/customerForms/external/uploadRevocationCertificateBlank?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    #endregion urls
}
