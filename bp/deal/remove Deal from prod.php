<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");



function getDealsByFilter_7($arFilter, $arSelect = array(), $arSort = array("ID"=>"DESC")) {
    $arDeals = array();
    $res = CCrmDeal::GetList($arSort, $arFilter, array("ID","STAGE_ID","CONTACT_ID"));
    while($arDeal = $res->Fetch()) array_push($arDeals, $arDeal);
    return (count($arDeals) > 0) ? $arDeals : false;
}

function getCIBlockElementsByFilter_7($arFilter = array(),$sort = array()){
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




function updateCIBlockElement($certificateId, $arForAdd, $arProps = array()) {
    $el = new CIBlockElement;
    $arForAdd["PROPERTY_VALUES"] = $arProps;
    if ($PRODUCT_ID = $el->Update($certificateId, $arForAdd)) return $PRODUCT_ID;
    else return 'Error: ' . $el->LAST_ERROR;
}




$root=$this->GetRootActivity();
$deal_ID=$root->GetVariable("deal_ID");


$arFilter=array("ID"=>$deal_ID);
$deal=getDealsByFilter_7($arFilter);




$prods = CCrmDeal::LoadProductRows($deal_ID);

foreach($prods as $prod){

    $arFilter=array("ID"=>$prod['PRODUCT_ID']);
    $Product=getCIBlockElementsByFilter_7($arFilter);

    $ProductID=$Product[0]["ID"];

 
    $Product[0]["DEAL"] = '';
    $Product[0]["deal_status"] = '';


    $arLoadProductArray = array(
        "PROPERTY_VALUES" => $Product[0],
        "NAME" => $Product[0]["NAME"],
        "ACTIVE" => "Y",
    );

    $el = new CIBlockElement;
    $res = $el->Update($ProductID, $arLoadProductArray);  
    
}


