<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Context;

class tableOut extends CBitrixComponent 
{

    private function _checkModules() 
    {
        if (!Loader::includeModule('iblock')) {
            throw new \Exception('Не загружены модули необходимые для работы');
        }
        return true;
    }

    public function executeComponent() 
    {

        $this->_checkModules();

        $request = Context::getCurrent()->getRequest();

        if ($request->get('arfolder')) $arFolder = $request->get('arfolder');

        $fetchResult = Array();

        if (isset($arFolder))
        {

            $arOrder = Array(
                'PROPERTY_URL_PAGE' => 'ASC'
            );

            $arFilter = Array(
                'IBLOCK_CODE' => 'CATALOG_SECTION_DATA',
                'IBLOCK_ID' => 100,
                'ACTIVE' => 'Y',
                '!CODE' => 'page',
                "PROPERTY_URL_PAGE"=>$arFolder."%"
            );

            $arSelect = Array(
                'ID',
                'NAME',
                'CODE',
                'PREVIEW_TEXT',
                'DETAIL_TEXT',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'PROPERTY_URL_PAGE',
                'PROPERTY_URL_BANNER',
                'PROPERTY_SECTION_NAME',
                'PROPERTY_CONSTRUCTOR_NAME',
                'PROPERTY_ID_BRAND',
                'PROPERTY_SEO_TITLE',
                'PROPERTY_SEO_H1',
                'PROPERTY_SEO_DESCRIPTION'
            );

            $arGroupBy = false;
            $arNavStartParams = false;

            $iBlockElementList = CIBlockElement::GetList(
                $arOrder,
                $arFilter,
                $arGroupBy,
                $arNavStartParams,
                $arSelect
            );

            while ($currentElementData = $iBlockElementList->Fetch())
            {
                if ($currentElementData['CODE'] == 'page-tab')
                {
                    $fetchResult['page-tab'][$currentElementData['ID']] = $currentElementData;
                } 
                else
                {
                    $fetchResult[] = $currentElementData;
                }
            }

        }

        $this->arResult['data'] = $fetchResult;

        $this->includeComponentTemplate();

    }

}
