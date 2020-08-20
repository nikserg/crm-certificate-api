<?php
/**
 * Created by PhpStorm.
 * User: n.zarubin
 * Date: 12.03.2020
 * Time: 11:05
 */

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Class FormData
 *
 * @package nikserg\CRMCertificateAPI\models\request\SendCustomerForm
 */
class SendCustomerFormData
{
    public $passportNumber;
    public $passportDate;
    public $passportIssuer;
    public $passportSeries;
    public $ownerPassportDeptCode;
    public $ownerBirthDate;

    public $email;
    public $phone;

    public $ownerLastName;
    public $ownerFirstName;
    public $ownerMiddleName;
    public $ownerPosition;
    public $ownerGender;
    public $departmentName;

    public $headLastName;
    public $headFirstName;
    public $headMiddleName;
    public $headPosition;
    public $authorityDocument;

    public $organizationShortName;
    public $INN;
    public $INNFL;
    public $INNIP;


    public $city;
    public $region;
    public $street;

    public $KPP;
    public $OGRN;
    public $OGRNIP;
    public $SNILS;
}