<?
//Класс, расширяющий функционал работы с ИБ Битрикса

CModule::IncludeModule('iblock');
class CustomPropertyEnum extends CIBlockPropertyEnum
{
	//Получаем id значения свойства
	static function GetIdSearch($code,$search,$iblock_id=false)
	{
		$result = false;
		if($code && $search)
		{
			$arFilter = Array("CODE"=>$code,"ID"=>$search);
			if($iblock_id)
				$arFilter["IBLOCK_ID"] = $iblock_id;
			$property_enums = self::GetList(Array(), $arFilter);
			if($enum_fields = $property_enums->GetNext())
			{
				$result = $enum_fields["ID"];
			}
			else
			{
				$arFilter = Array("CODE"=>$code,"XML_ID"=>$search);
				if($iblock_id)
					$arFilter["IBLOCK_ID"] = $iblock_id;				
				$property_enums = self::GetList(Array(), $arFilter);
				if($enum_fields = $property_enums->GetNext())
				{
					$result = $enum_fields["ID"];
				}
				else
				{
					$arFilter = Array("CODE"=>$code,"VALUE"=>$search);
					if($iblock_id)
						$arFilter["IBLOCK_ID"] = $iblock_id;					
					$property_enums = self::GetList(Array(), $arFilter);
					if($enum_fields = $property_enums->GetNext())
					{
						$result = $enum_fields["ID"];
					}
				}
			}
		}	
		return $result;
	}
	//Получаем все ID Значений какого-то свойства
	public static function GetIdArrayProp($code,$iblock_id=false)
	{
		$result = array();
		$arFilter = Array("CODE"=>$code);
		if($iblock_id)
			$arFilter["IBLOCK_ID"] = $iblock_id;
		$property_enums = self::GetList(Array("SORT"=>"ASC"), $arFilter);
		while($enum_fields = $property_enums->GetNext())
		{
			$result[$enum_fields["XML_ID"]] = $enum_fields["ID"];
		}		
		return $result;
	}
	//Получем всю информацию о значениях какого-то свойства
	public static function GetArrayProp($code,$iblock_id=false)
	{
		$result = array();
		$arFilter = Array("CODE"=>$code);
		if($iblock_id)
			$arFilter["IBLOCK_ID"] = $iblock_id;
		$property_enums = self::GetList(Array(), $arFilter);
		while($enum_fields = $property_enums->GetNext())
		{
			$result[$enum_fields["XML_ID"]] = $enum_fields;
		}		
		return $result;
	}	
}

//Пример вызова:
//CustomPropertyEnum::GetIdSearch("LIST_PROPERTY_CODE","LIST_PROPERTY_CODE_VALUE",IBLOCK_FORM_LANDING);

class CustomCIBlockElement extends CIBlockElement
{
	//Получение значение свойства по ID элемента и Коду свойства
	public static function GetPropertyValue($ID,$CODE,$IBLOCK_ID)
	{
		$result = "";
		$db_props = self::GetProperty($IBLOCK_ID, $ID, array(), array("CODE"=>$CODE));
		if($ar_props = $db_props->GetNext())
		{
			$result = $ar_props['VALUE'];
		}
		return $result;
	}
	//Получение элемента по коду
	public static function GetByCode($CODE,$IBLOCK_ID="")
	{
		$result = array();
		$arSelect = Array();
		$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$CODE);
		$res = self::GetList(Array("ID"=>"DESC"), $arFilter, false, Array("nPageSize"=>1));
		if($ob = $res->GetNextElement())
		{ 
			$arFields = $ob->GetFields();  
			$arProps = $ob->GetProperties();
			$arFields["PROPERTIES"] = $arProps;
			$result = $arFields;
		}		
		return $result;
	}

}

//Пример вызова:
//CustomCIBlockElement::GetPropertyValue($element_id,"PROPERTY_CODE","IBLOCK_ID");

