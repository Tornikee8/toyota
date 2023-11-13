<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");



function getCIBlockElementsByFilter_7($arFilter = array(),$sort = array()){
    $arElements = array();
    $arSelect = array("ID", "CATEGORY_ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");
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




// $root=$this->GetRootActivity();
// $deal_ID=$root->GetVariable("deal_ID");

$deal_ID=43;


$canSell = "NO";

$prods = CCrmDeal::LoadProductRows($deal_ID);

foreach($prods as $prod){

    $arFilter=array("ID"=>$prod['PRODUCT_ID']);
    $Product=getCIBlockElementsByFilter_7($arFilter);

    $categoryID=$Product[0]["CATEGORY_ID"];
    
    if($categoryID == "16"){
        $canSell = "YES";
    }else{
        $canSell = "NO";
    }
    
}

echo $canSell;

// $this->SetVariable("canSell" , $canSell);
