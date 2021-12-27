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

        // По условиям задачи должна быть переменная $arFolder, и если
        // она у нее есть значение, тогда нужно получить данные из инфоблока.
        // Для примера возьмем значение этой переменной из GET параметра "arfolder"

        if ($request->get('arfolder')) $arFolder = $request->get('arfolder');

        // Инициализация пустого результирующего массива 

        $fetchResult = Array();

        // Если переменная $arFolder содержит значение

        if (isset($arFolder))
        {

            // Данные о сортировке выборки

            $arOrder = Array(
                'PROPERTY_URL_PAGE' => 'ASC'
            );

            // Данные для фильтрации выборки

            $arFilter = Array(
                'IBLOCK_ID' => 100, // Получать элементы инфоблока с ID = 100
                'ACTIVE' => 'Y',
                '!CODE' => 'page', // Получать только те элементы, у которых поле CODE не равно "page"
                "PROPERTY_URL_PAGE"=>$arFolder."%" // Получать только те элементы, у которых значение свойства URL_PAGE начинается с $arFolder или ей равно
            );

            // Список получаемых элементов
            // Прим.: здесь можно указать просто false, но я не понял из условия задачи, нужно получать только указанные в условиях данные, или можно получать все данные

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

            // Неиспользуемые параметры в рамках задачи
            $arGroupBy = false;
            $arNavStartParams = false;

            // Выборка элементах инфоблоков в соответствии с описанными выше критериями

            $iBlockElementList = CIBlockElement::GetList(
                $arOrder,
                $arFilter,
                $arGroupBy,
                $arNavStartParams,
                $arSelect
            );

            // В цикле получаем данные элементов инфоблока и наполняем результирующий массив

            while ($currentElementData = $iBlockElementList->Fetch())
            {

                // Если символьный код текущего элемента равен "page-tab", 
                // то данные добавляются в результриующий массив в формате
                // ['значение поля CODE элемента инфоблока']['значение поля ID элемента инфоблока'] => [массив со всеми наполняемыми данными текущего элемента инфоблока CATALOG_SECTION_DATA]

                // В противном случае формат добавляемого элемента результриующего массива должен быть следующим:
                // [автоинкримент] => [массив со всеми наполняемыми данными текущего элемента инфоблока CATALOG_SECTION_DATA]

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
