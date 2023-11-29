<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");


function printArr($arr) {
    echo "<pre>"; print_r($arr); echo "</pre>";
}


function getCIBlockElementsByFilter_check_if_deal($arFilter = array(),$sort = array()){
    $arElements = array();
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");
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

// $deal_ID = 65;


$prods = CCrmDeal::LoadProductRows($deal_ID);

$canReserve="YES";

foreach($prods as $prod){

    $arFilter=array("ID"=>$prod['PRODUCT_ID']);
    $Product=getCIBlockElementsByFilter_check_if_deal($arFilter);


    if($Product[0]["DEAL"] && $Product[0]["DEAL"]!=$deal_ID){
        $canReserve=$Product[0]["DEAL"];
    }


    
}


$this->SetVariable("canReserve" , $canReserve);