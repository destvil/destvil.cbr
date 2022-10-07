<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle('Список валют');

$APPLICATION->IncludeComponent('destvil.cbr:currency.grid', '');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
