<?
CModule::IncludeModule("sale");

AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array('CFTeaCourier', 'Init'));

class CFTeaCourier
{
	function Init()
	{
		return array(
			/* Основное описание */
			"SID" => "courier",
			"NAME" => "Доставка нашей ТК по Москве и МО",
			"DESCRIPTION" => "Доставка в течение дня",
			"DESCRIPTION_INNER" => "Доставка в течение дня",
			"BASE_CURRENCY" => COption::GetOptionString("sale", "default_currency", "RUB"),
			"HANDLER" => __FILE__,

			/* Методы обработчика */
			"DBGETSETTINGS" => array("CFTeaCourier", "GetSettings"),
			"DBSETSETTINGS" => array("CFTeaCourier", "SetSettings"),
			"GETCONFIG" => array("CFTeaCourier", "GetConfig"),
			"COMPABILITY" => array("CFTeaCourier", "Compability"),
			"CALCULATOR" => array("CFTeaCourier", "Calculate"),

			/* Список профилей доставки */
			"PROFILES" => array(
				"courier" => array(
					"TITLE" => "По Москве и МО",
					"DESCRIPTION" => "",
					"RESTRICTIONS_WEIGHT" => array(0), // без ограничений
					"RESTRICTIONS_SUM" => array(0), // без ограничений
				),
			)
		);
	}

	/**
	 * настройки обработчика
	 */
	function GetConfig()
	{
		$arConfig = array(
			"CONFIG_GROUPS" => array(),
			"CONFIG" => array(),
		);

		return $arConfig;
	}

	/**
	 * подготовка настроек для занесения в базу данных
	 */
	function SetSettings($arSettings)
	{
		foreach ($arSettings as $key => $value) {
			if (strlen($value) > 0) {
				$arSettings[$key] = doubleval($value);
			} else {
				unset($arSettings[$key]);
			}
		}

		return serialize($arSettings);
	}

	/**
	 * подготовка настроек, полученных из базы данных
	 */
	function GetSettings($strSettings)
	{
		return unserialize($strSettings);
	}

	/**
	 * метод проверки совместимости
	 */
	function Compability($arOrder, $arConfig)
	{ 
		return array('courier');
	}

	/**
	 * собственно, рассчет стоимости
	 */
	function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
	{
			$mult = 1;
			$sum = 0; // итоговая сумма
            $qty = array(); // количества
            $items = array(); // товары
			$first_price = 0;
            $rsCart = CSaleBasket::GetList(array(), array('FUSER_ID'=>CSaleBasket::GetBasketUserID(), 'LID'=>SITE_ID, 'ORDER_ID'=>null));
            while ($arCartItem = $rsCart->Fetch()) {
                if ($arCartItem['PRODUCT_ID']) {
					if($first_price == 0){
					//для каждого получаем - Доставку 1, Доставку 2 и количества.
					$res = CIBlockElement::GetProperty(28, $arCartItem["PRODUCT_ID"], "sort", "desc", Array("CODE" => "dostavka1"));
					while ($ob = $res->GetNext())
					{
					
                    $first_price = $first_price +  $ob['VALUE'];
					}
                    $res = CIBlockElement::GetProperty(28, $arCartItem["PRODUCT_ID"], "sort", "desc", Array("CODE" => "dostavka2"));
					while ($ob = $res->GetNext())
					{
					
                    $other_price = $other_price + ($arCartItem['QUANTITY'] - 1)*$ob['VALUE'];
					}
                }
				else {
				 $res = CIBlockElement::GetProperty(265, $arCartItem["PRODUCT_ID"], "sort", "asc", Array("CODE" => "dostavka2"));
					while ($ob = $res->GetNext())
					{
					
                    $other_price = $other_price + ($arCartItem['QUANTITY'])*$ob['VALUE'];
					}
				
				}
				}
            }

			$iTotal = $first_price+$other_price; 
	
	
		return array(
			"RESULT" => "OK",
			"VALUE" => $iTotal
		);
	}
}
?>
