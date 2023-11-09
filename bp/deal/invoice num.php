<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");


/// bp 6


function getDealsByFilter_6($arFilter, $arSelect = array(), $arSort = array("ID"=>"DESC")) {
    $arDeals = array();
    $res = CCrmDeal::GetList($arSort, $arFilter, array("ID","STAGE_ID","CONTACT_ID"));
    while($arDeal = $res->Fetch()) array_push($arDeals, $arDeal);
    return (count($arDeals) > 0) ? $arDeals : false;
}

function getCIBlockElementsByFilter_6($arFilter = array(),$sort = array()){
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
$ProductID=$root->GetVariable("element_ID");


$arFilter=array("ID"=>$deal_ID);
$deal=getDealsByFilter_6($arFilter);


$contactID=$deal[0]['CONTACT_ID'];


$arFilter=array("ID"=>$ProductID);
$Product=getCIBlockElementsByFilter_6($arFilter);



$prodDeal = $Product[0]["DEAL"];


$invoiceNums = $Product[0]["Invoice_num"];
$contacts = $Product[0]["CLIENTS"];
$dealHistory = $Product[0]["deal_history"];


if(!$prodDeal){

    $arFilter=array("ID"=>44);
    $invoiceNumElem=getCIBlockElementsByFilter_6($arFilter);

    $lastInvoiceNum = $invoiceNumElem[0]["NUM"];
    $lastInvoiceNum = $lastInvoiceNum + 1;


    $invoiceNumElem[0]["NUM"] = $lastInvoiceNum;
    

    $arLoadProductArray = array(
        "PROPERTY_VALUES" => $invoiceNumElem[0],
        "NAME" => $invoiceNumElem[0]["NAME"],
        "ACTIVE" => "Y",
    );

    $el = new CIBlockElement;
    $res = $el->Update(44, $arLoadProductArray);


    $invoiceNumLength=strlen($lastInvoiceNum);


    switch($invoiceNumLength) {
        case 1:
            $lastInvoiceNum = "TCT00000$lastInvoiceNum";
            break;
        case 2:
            $lastInvoiceNum = "TCT0000$lastInvoiceNum";
            break;
        case 3:
            $lastInvoiceNum = "TCT000$lastInvoiceNum";
            break;
        case 4:
            $lastInvoiceNum = "TCT00$lastInvoiceNum";
            break;
        case 5:
            $lastInvoiceNum = "TCT0$lastInvoiceNum";
            break;
    }

    if($invoiceNums){
        array_push($invoiceNums , $lastInvoiceNum);
    }else{
        $invoiceNums = array();
        array_push($invoiceNums , $lastInvoiceNum);
    }

    if($contacts){
        array_push($contacts , $contactID);
    }else{
        $contacts = array();
        array_push($contacts , $contactID);
    }

    
    if($dealHistory){
        array_push($dealHistory , $deal_ID);
    }else{
        $dealHistory = array();
        array_push($dealHistory , $deal_ID);
    }


    $Product[0]["DEAL"] = $deal_ID;
    $Product[0]["Invoice_num"] = $invoiceNums;
    $Product[0]["CLIENTS"] = $contacts;
    $Product[0]["deal_history"] = $dealHistory;
    $Product[0]["deal_status"] = 87;

    $arLoadProductArray = array(
        "PROPERTY_VALUES" => $Product[0],
        "NAME" => $Product[0]["NAME"],
        "ACTIVE" => "Y",
    );

    $el = new CIBlockElement;
    $res = $el->Update($ProductID, $arLoadProductArray);  
    
    
    $CCrmDeal = new CCrmDeal();
    $upd = array(
        "UF_CRM_1699001429021" => $lastInvoiceNum,
    );
    $CCrmDeal->Update($deal_ID, $upd);

}
