<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Модель для передачи файла запроса (req-файла) в CRM
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendReqFile
{
    /**
     * @var int ID заявки
     */
    public $id;

    /**
     * @var string Base64-строка с содержимым файла запроса
     */
    public $content;

    /**
     * @var string Имя контейнера на компьютере пользователя
     */
    public $container;

    /**
     * @var ?string Информация о ключевом носителе
     */
    public $containerInfo;

    /**
     * @param int $id
     * @param string $content
     * @param string $container
     * @param ?string $containerInfo
     */
    public function __construct($id, $content, $container, $containerInfo = null)
    {
        $this->id = $id;
        $this->content = $content;
        $this->container = $container;
        $this->containerInfo = $containerInfo;
    }
}
