<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use destvil\cbr\Control\CurrencyTable;

class destvil_cbr extends CModule {
    var $MODULE_ID = 'destvil.cbr';
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');
        if (!empty($arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('DESTVIL_CBR_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DESTVIL_CBR_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('DESTVIL_CBR_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('DESTVIL_CBR_PARTNER_URI');
    }

    function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);

        $this->InstallFiles();
        $this->InstallDB();
    }

    function DoUninstall()
    {
        Loader::includeModule($this->MODULE_ID);

        $this->UnInstallDB();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        $connection = Application::getConnection();

        $currencyEntity = CurrencyTable::getEntity();
        if (!$connection->isTableExists($currencyEntity->getDBTableName())) {
            $currencyEntity->createDbTable();
        }

        $this->InstallAgents();
    }

    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $this->UnInstallAgents();

        $connection = Application::getConnection();

        if ($connection->isTableExists(CurrencyTable::getTableName())) {
            $connection->dropTable(CurrencyTable::getTableName());
        }
    }

    public function InstallAgents()
    {
        $nextExecDate = new \Bitrix\Main\Type\DateTime();
        $nextExecDate->setTime(0, 0);

        CAgent::AddAgent(
            '\destvil\cbr\Boundary\CurrencySynchronizerAgent::execute();',
            $this->MODULE_ID,
            'Y',
            86400,
            '',
            'Y',
            ConvertTimeStamp($nextExecDate->getTimestamp(), 'FULL')
        );
    }

    private function UnInstallAgents()
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    public function InstallFiles(): bool
    {
        $filesPath = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/destvil.cbr/install/files';

        CopyDirFiles($filesPath . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components/destvil.cbr', true, true);
        CopyDirFiles($filesPath . '/public', $_SERVER['DOCUMENT_ROOT'] . '/');

        return true;
    }

    public function UnInstallFiles(): bool
    {
        DeleteDirFilesEx('/local/components/destvil.cbr');

        return true;
    }
}