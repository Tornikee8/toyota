<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");

function printArr($arr) {
    echo "<pre>"; print_r($arr); echo "</pre>";
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


function getDealsByFilter_7a($arFilter, $arSelect = array(), $arSort = array("ID"=>"DESC")) {
    $arDeals = array();
    $res = CCrmDeal::GetList($arSort, $arFilter, array("ID","STAGE_ID","CONTACT_ID","UF_CRM_1699001429021"));
    while($arDeal = $res->Fetch()) array_push($arDeals, $arDeal);
    return (count($arDeals) > 0) ? $arDeals : false;
}


$root=$this->GetRootActivity();
$deal_ID=$root->GetVariable("deal_ID");

// $deal_ID = 90;

$arFilter=array("ID"=>$deal_ID);
$deal=getDealsByFilter_7a($arFilter);


$prods = CCrmDeal::LoadProductRows($deal_ID);


if($deal[0]["UF_CRM_1699001429021"]){


    foreach($prods as $prod){

        $arFilter=array("ID"=>$prod['PRODUCT_ID']);
        $Product=getCIBlockElementsByFilter_7($arFilter);
    
        $ProductID=$Product[0]["ID"];
    
        $Product[0]["DEAL"] = $deal_ID;
        $Product[0]["deal_status"] = 87;
    
        $arLoadProductArray = array(
            "PROPERTY_VALUES" => $Product[0],
            "NAME" => $Product[0]["NAME"],
            "ACTIVE" => "Y",
        );
    
        $el = new CIBlockElement;
        $res = $el->Update($ProductID, $arLoadProductArray);  
  

    }
    

}else{

    
    $arFilter=array("ID"=>44);
    $invoiceNumElem=getCIBlockElementsByFilter_7($arFilter);

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


    foreach($prods as $prod){

        $arFilter=array("ID"=>$prod['PRODUCT_ID']);
        $Product=getCIBlockElementsByFilter_7($arFilter);
    
        $ProductID=$Product[0]["ID"];
    
        $Product[0]["Invoice_num"] = $invoiceNums;
        $Product[0]["DEAL"] = $deal_ID;
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
        
}

