<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");

function printArr($arr) {
    echo "<pre>"; print_r($arr); echo "</pre>";
}


function getCIBlockElementsByFilter_7S($arFilter = array(),$sort = array()){
    $arElements = array();
    $arSelect = array("ID", "IBLOCK_SECTION_ID", "NAME", "DATE_ACTIVE_FROM");
    $res = CIBlockElement::GetList($sort, $arFilter, false, array("nPageSize" => 50), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFilds = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arPushs = array();
        foreach ($arFilds as $key => $arFild) $arPushs[$key] = $arFild;
        foreach ($arProps as $key => $arProp) $arPushs[$key] = $arProp["VALUE"];
        array_push($arElements, $arPushs);
    }
    return $arElements;
}




$root=$this->GetRootActivity();
$deal_ID=$root->GetVariable("deal_ID");

// $deal_ID=36;


$canSell = "NO";

$prods = CCrmDeal::LoadProductRows($deal_ID);

foreach($prods as $prod){


    $arFilter=array("ID"=>$prod['PRODUCT_ID']);
    $Product=getCIBlockElementsByFilter_7S($arFilter);
    $categoryID=$Product[0]["IBLOCK_SECTION_ID"];
    
    if($categoryID == "16"){
        $canSell = "YES";
    }else{
        $canSell = "NO";
    }
    
}

$this->SetVariable("canSell" , $canSell);
