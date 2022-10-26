<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

// AJAX data
$IBprop=$_POST["IB"];// 84
$pageSize=$_POST["pageSize"];// 5
$pageNum=$_POST["pageNum"];// 1
//$section=$_POST["section"];// 1


$result["IB"]=$IBprop;
$result["pageSize"]=$pageSize;
$result["pageNum"]=$pageNum;
//$result["section"]=$section;


$result["error"] = '';

if($IBprop > 0){
	// Scan Prop IB
	$arFilter = Array("IBLOCK_ID"=>$IBprop, 'INCLUDE_SUBSECTIONS'=>'Y');
	$arPager = Array("nPageSize"=>$pageSize, "iNumPage"=> $pageNum);
	$arSelect = Array("ID", "IBLOCK_ID", "NAME");
	$res = CIBlockElement::GetList(array(), $arFilter, false, $arPager, $arSelect);
	while($ob = $res->GetNextElement()){
		$arFields = $ob->GetFields();

		// Check base IB to chain
		$hasBase = array();
		$arFilterProp = Array("IBLOCK_ID"=>28, "PROPERTY_228"=> $arFields["ID"]);
		$arSelectProp = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_228");
		$resProp = CIBlockElement::GetList(array(), $arFilterProp, false, array(), $arSelectProp);
		while($obProp = $resProp->GetNextElement()){
			$arBase = $obProp->GetFields();
			$hasBase[] = $arBase["ID"];
		}
			
		if(!empty($hasBase)){
			
			// Prop Conv
			$arProps = $ob ->GetProperties();
			$translateProp = array();
			foreach($arProps as $key => $prop){
				if($prop["VALUE"] !=''){// work with only string
					
					if($key == 1391)// Hook for IB 84
						$translateProp[6830] = $prop["VALUE"];		
					else
						$translateProp[$key] = $prop["VALUE"];
				}
			}
			
			if(!empty($translateProp)){
				foreach($hasBase as $baseElem){// Prop upd
					CIBlockElement::SetPropertyValuesEx($baseElem, 28, $translateProp);
					$result["items"] .= $arFields["ID"]."__".$baseElem."<br>";
				}				
			}else{
				$result["items"] .= $arFields["ID"]."_noPROP<br>";
			}

		}else{
			$result["items"] .= $arFields["ID"]."_noBASE<br>";
		}
	}
	
	//
	$result["curPart"] = $res->NavPageNomer;
	$result["totalParts"] = $res->NavPageCount;
}else{
	$result["error"] = 'Error IB ID';
}

echo json_encode($result);
?>