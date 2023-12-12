<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\GroupTable;

class UserGroupsListComponent extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $arParams["CACHE_TIME"] = isset($arParams["CACHE_TIME"]) ? $arParams["CACHE_TIME"] : 36000000;
        return $arParams;
    }

    public function executeComponent()
    {
        if($this->startResultCache($this->arParams["CACHE_TIME"])) {
            if (!Loader::includeModule('main')) {
                $this->abortResultCache();
                ShowError("Модуль 'main' не установлен");
                return;
            }

            $result = GroupTable::getList(array(
                'select'  => array('ID', 'NAME', 'DESCRIPTION')
            ));

            $this->arResult = $result->fetchAll();

            $this->includeComponentTemplate();
        }
    }
}
?>