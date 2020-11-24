<?php

namespace nikserg\CRMCertificateAPI\tests;

use nikserg\CRMCertificateAPI\models\response\Esia\Egrul;
use PHPUnit\Framework\TestCase;

class EgrulTest extends TestCase
{
    public function testLegal()
    {
        $egrul = new Egrul(json_decode('{
    "id": 1228721,
    "type": "egrul",
    "status": 2,
    "comment": "Выписка ЕГРЮЛ получена #1228721",
    "created": "2020-11-24 12:55:06",
    "data": {
        "organizationShortName": "ООО \"ИТК\"",
        "organizationFullName": "ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ \"ИНТЕРНЕТ ТЕХНОЛОГИИ И КОММУНИКАЦИИ\"",
        "OGRN": "1112310000220",
        "INN": "2310152134",
        "KPP": "230801001",
        "fiasAddress": "КРАЙ КРАСНОДАРСКИЙ, ГОРОД КРАСНОДАР, УЛИЦА ДАЛЬНЯЯ, ДОМ 39/3, ПОМЕЩЕНИЕ 140",
        "rawParticipators": [
            "  ",
            "  ",
            "  "
        ],
        "rawRegion": "КРАЙ КРАСНОДАРСКИЙ",
        "rawCity": "КРАСНОДАР",
        "rawOffice": "ПОМЕЩЕНИЕ 140",
        "rawHouse": "ДОМ 39/3",
        "rawStreet": "УЛИЦА ДАЛЬНЯЯ",
        "postcode": "350051",
        "region": "23 Краснодарский край",
        "city": "Краснодар",
        "street": "УЛИЦА ДАЛЬНЯЯ, ДОМ 39/3, ПОМЕЩЕНИЕ 140",
        "headLastName": "Сорокин",
        "headFirstName": "Дмитрий",
        "headMiddleName": "Викторович",
        "headPosition": "Генеральный директор"
    }
}'));
        $this->assertEquals('УЛИЦА ДАЛЬНЯЯ, ДОМ 39/3, ПОМЕЩЕНИЕ 140', $egrul->address->street);
        $this->assertEquals('Краснодар', $egrul->address->city);
        $this->assertEquals('230801001', $egrul->kpp);
        $this->assertEquals('2310152134', $egrul->inn);
        $this->assertEquals('1112310000220', $egrul->ogrn);
        $this->assertEquals('Сорокин Дмитрий Викторович', $egrul->head->getFIO());
        $this->assertEquals('Генеральный директор', $egrul->head->jobName);
        $this->assertEquals('ООО "ИТК"', $egrul->shortName);
    }
}