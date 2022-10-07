<?php

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var CBitrixComponentTemplate $this
 * @var array $arParams
 * @var array $arResult
 */

global $APPLICATION;

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $arResult['GRID_ID'],
        'HEADERS' => $arResult['HEADERS'],
        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
        'ROWS' => $arResult['ROWS'],
        'SHOW_PAGESIZE' => $arResult['SHOW_PAGESIZE'],
        'PAGE_SIZES' => $arResult['PAGE_SIZES'],
        'SHOW_CHECK_ALL_CHECKBOXES' => $arResult['SHOW_CHECK_ALL_CHECKBOXES'],
        'SHOW_ROW_CHECKBOXES' => $arResult['SHOW_ROW_CHECKBOXES'],
        'AJAX_MODE' => $arResult['AJAX_MODE'],
    ]
);