<?
ob_start();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("createProd");



function addElement($infoArr) {

    $IBLOCK_ID = 14;
    $el = new CIBlockElement();

 

    $PROP = array(
        "_4PU2NQ" => $infoArr[0],
        "INVOICE__LCLV7R" => $infoArr[1],
        "PROD_MON_Z1ICNY" => $infoArr[2],
        "STOCKING_DATE_LCAFN6" => $infoArr[3],
        "VEHICLE_VZMJ9H" => $infoArr[4],
        "ENGINE_1BWQM4" => $infoArr[5],
        "MODEL_CODE_6HLW6F" => $infoArr[6],
        "SFX_CT8L4X" => $infoArr[7],
        "CHASSIS_X0IZII" => $infoArr[8],
        "SFX_CT8L4X" => $infoArr[9],
        "EXT_TTIJXI" => $infoArr[10],
        "INT_LMYJKX" => $infoArr[11],        
        "KEY_XE1GGS" => $infoArr[12],
        "LOCATION_AY82FS" => $infoArr[13],
        "VENDOR_BWFR1K" => $infoArr['vendor'],
        "PRICE__8S8T3Z" => $infoArr[14],
        "PRICE_GEL_BS052Y" => $infoArr[15],
        
        
    );

    $arLoadProductArray = Array(
        "IBLOCK_ID" => $IBLOCK_ID,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => $infoArr[8],
        "ACTIVE"         => "Y",
        "IBLOCK_SECTION_ID" => 16,

    );
    // printArr($arLoadProductArray);
    $res = $el->Add($arLoadProductArray);
    
    if($res){
        
        $CCatalogProduct = new CCatalogProduct();
        $arCatalogProductFields = array(
            "ID" => $res,
            "MEASURE" => $measure,
            "CURRENCY" => 'GEL',
            "VAT_ID" => 0,
            
        );

        // ADD UNIT
        $resProdCatalog = $CCatalogProduct->Add($arCatalogProductFields);

        $arFields2 = Array(
            "PRODUCT_ID" => $res,
            "CATALOG_GROUP_ID" => 1,
            "PRICE" => 0,
            "CURRENCY" => "GEL"
        );
        // ADD PRICE
        CPrice::Add($arFields2);
    }

    if($res){
        return $infoArr[14];
    }
    return $res;
}



function getCIBlockElementsByFilter($arFilter = array()) {
    $arElements = array();
    // $arSelect = Array("ID","IBLOCK_ID","NAME","DATE_ACTIVE_FROM","PROPERTY_*");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
    while($ob = $res->GetNextElement()) {
        $arFilds = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arPushs = array();
        foreach($arFilds as $key => $arFild) $arPushs[$key] = $arFild;
        foreach($arProps as $key => $arProp) $arPushs[$key] = $arProp["VALUE"];
        $arPushs["image"]    = CFile::GetPath($arPushs["DETAIL_PICTURE"]);
        $arPushs["image1"]    = CFile::GetPath($arPushs["PREVIEW_PICTURE"]);

        array_push($arElements, $arPushs);
    }
    return $arElements;
}



$errors = array();
$json = array();

try {
    $json = \Bitrix\Main\Web\Json::decode(\Bitrix\Main\HttpRequest::getInput());
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}


$prods=$json;

$resArr=array();




foreach($prods['old'] as $prod){


    $chassis=$prod['8'];

    $arFilter = array(
        "PROPERTY_CHASSIS_X0IZII" => $chassis,
    );
    
    $Product = getCIBlockElementsByFilter($arFilter);

    $ProductID=$Product[0]['ID'];

    $Product[0]["VENDOR_BWFR1K"] = $prod['vendor'];
    $Product[0]["PRICE__8S8T3Z"] = $prod[14];
    $Product[0]["PRICE_GEL_BS052Y"] = $prod[15];

    $arLoadProductArray = array(
        "PROPERTY_VALUES" => $Product[0],
        "NAME" => $Product[0]["NAME"],
        "IBLOCK_SECTION_ID" => 16,
        "ACTIVE" => "Y",
    );
    

    
    $el = new CIBlockElement;
    $res = $el->Update($ProductID, $arLoadProductArray);
    

    $resArr=$prod;

    
   

}


foreach($prods['new'] as $prod){

    $res = addElement($prod);
    array_push($resArr,$res);

}

$response=$json;


ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);