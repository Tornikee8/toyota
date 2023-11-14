<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");



function getCIBlockElementsByFilter_7B($arFilter = array(),$sort = array()){
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




$prods = CCrmDeal::LoadProductRows($deal_ID);

foreach($prods as $prod){

    $arFilter=array("ID"=>$prod['PRODUCT_ID']);
    $Product=getCIBlockElementsByFilter_7B($arFilter);

    $ProductID=$Product[0]["ID"];

    if($Product[0]["deal_status"] == "დაჯავშნილი"){
        $Product[0]["deal_status"] = 87;
    }elseif($Product[0]["deal_status"] == "კალკულაციის მოლოდინში"){
        $Product[0]["deal_status"] = 88;
    }
      

    $Product[0]["STATUS_SUB"] = "გადახდის პროცესში";


    $arLoadProductArray = array(
        "PROPERTY_VALUES" => $Product[0],
        "NAME" => $Product[0]["NAME"],
        "ACTIVE" => "Y",
    );

    $el = new CIBlockElement;
    $res = $el->Update($ProductID, $arLoadProductArray);  
    
}


