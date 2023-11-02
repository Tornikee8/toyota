<?
ob_start();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");



function printArr($arr) {
    echo "<pre>"; print_r($arr); echo "</pre>";
}

// bp 6


function getCIBlockElementsByFilter($arFilter = array()) {
    $arElements = array();
    $arSelect = Array("ID","IBLOCK_ID","IBLOCK_SECTION_ID","NAME","DATE_ACTIVE_FROM","PROPERTY_*");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    while($ob = $res->GetNextElement()) {
        $arFilds = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arPushs = array();
        foreach($arFilds as $key => $arFild) $arPushs[$key] = $arFild;
        foreach($arProps as $key => $arProp) $arPushs[$key] = $arProp["VALUE"];
        $price      = CPrice::GetBasePrice($arPushs["ID"]);
       $arPushs["PRICE"] = $price["PRICE"];
        array_push($arElements, $arPushs);
    }
    return $arElements;
}





$root=$this->GetRootActivity();
$deal_ID=$root->GetVariable("deal_ID");
$element_ID=$root->GetVariable("element_ID");

$arFilter=array("ID" => $element_ID);
$prod = getCIBlockElementsByFilter($arFilter);

$Product[0]["DEAL"] = $deal_ID;
    

$arLoadProductArray = array(
    "PROPERTY_VALUES" => $Product[0],
    "NAME" => $Product[0]["NAME"],
    "ACTIVE" => "Y",
);

    
$el = new CIBlockElement;
$res = $el->Update($element_ID, $arLoadProductArray);