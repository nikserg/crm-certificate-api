<?php

namespace nikserg\CRMCertificateAPI;


use nikserg\CRMCertificateAPI\models\data\Status;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\GetPassportCheck;
use nikserg\CRMCertificateAPI\models\response\GetSnilsCheck;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm;
use nikserg\CRMCertificateAPI\models\response\Esia;
use nikserg\CRMCertificateAPI\models\response\Esia\GetEgrul;

/**
 * Class MockClient
 *
 * Тестовый клиент для связи с API CRM
 *
 * @package nikserg\CRMCertificateAPI
 */
class MockClient extends Client
{
    public function getEgrul($customerFormCrmId)
    {
        return new GetEgrul(json_encode([
            "id"       => 1,
            "status"   => Esia::STATUS_SUCCESS,
            "response" => [
                'organizationShortName' => 'OOO',
                'organizationFullName'  => 'Obwestwo s ogranichenoy otvetstvenostu',
                'OGRN'                  => '1234567890',
                'INN'                   => '1234567890',
                'KPP'                   => '12345678',
                'region'                => 'KRD',
                'city'                  => 'Krasnodar',
                'street'                => 'One way st.',
                'fiasAddress'           => 'Krasnodar, One way st. 1337',
                'headFirstName'         => 'Ivan',
                'headMiddleName'        => 'Ivanov',
                'headLastName'          => 'Ivanovi4',
                'headPosition'          => 'The head',
            ],
        ]));
    }

    public function getCustomerFormCertificateBlank($customerFormCrmId, $format = 'pdf')
    {
        return base64_encode("Imagine this is binary pdf content");
    }

    public function getCustomerFormClaim($customerFormCrmId, $format = 'pdf')
    {
        return base64_encode("Imagine this is binary pdf content");
    }

    public function sendCustomerForm(SendCustomerFormRequest $customerForm)
    {
        $response = new SendCustomerFormResponse();
        $response->id = 1;
        $response->token = 'crmToken';
        $response->generationToken = 'crmGenerateToken';
        return $response;
    }

    public function getCustomerForm($customerFormCrmId)
    {
        $response = new GetCustomerForm();
        $response->status = Status::CERTIFICATE;
        $response->tokenCertificate = 'crmToken';
        $response->opportunityId = 1;
        return $response;
    }

    public function getOpportunity($opportunityCrmId)
    {
        $response = new GetOpportunity();
        $response->isPay = true;
        $response->accountId = 1;
        $response->paymentToken = 'paymentToken';
        return $response;
    }

    public function changeStatus(ChangeStatus $changeStatus)
    {
        $response = new BooleanResponse();
        $response->status = true;
        return $response;
    }

    public function deleteCustomerForm($customerFormCrmId)
    {
        $response = new BooleanResponse();
        $response->status = true;
        return $response;
    }

    public function sendCustomerFormData($crmCustomerFormId, SendCustomerFormData $customerFormData)
    {
        $response = new SendCustomerFormResponse();
        $response->id = 1;
        $response->token = 'crmToken';
        $response->generationToken = 'crmGenerateToken';
        return $response;
    }

    public function getPassportCheck($series, $number)
    {

        $response = new GetPassportCheck();
        $response->comment = '';
        $response->status = true;
        return $response;
    }

    public function getSnilsCheck($customerFormCrmId)
    {
        $response = new GetSnilsCheck();
        $response->status = GetSnilsCheck::STATUS_SUCCESS;
        $response->message = '';
        $response->id = 1;

        return $response;
    }
}