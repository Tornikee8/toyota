<?
ob_start();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("createProd");


function addElement($infoArr) {
    $IBLOCK_ID = 14;
    $el = new CIBlockElement();
    // printArr($infoArr);
 

    $PROP = array(
        "_4PU2NQ" => $infoArr[0],
        "INVOICE__LCLV7R" => $infoArr[1],
        "PROD_MON_Z1ICNY" => $infoArr[2],
        "SHIP_DATE_TA6TRF" => $infoArr[3],
        "ETA_TBS_XI2PZH" => $infoArr[4],
        "ETA_TBS_1_4HS3XD" => $infoArr[5],
        "STOCKING_DATE_LCAFN6" => $infoArr[6],
        "VEHICLE_VZMJ9H" => $infoArr[7],
        "ENGINE_1BWQM4" => $infoArr[8],
        "MODEL_CODE_6HLW6F" => $infoArr[9],
        "ENGINE_CC_2477XT" => $infoArr[10],
        "DAMAGE_TYPE_IP4S35" => $infoArr[11],
        "SFX_CT8L4X" => $infoArr[12],
        "ED_NO_X0FX1V" => $infoArr[13],
        "CHASSIS_X0IZII" => $infoArr[14],
        "URN_YJ9OX3" => $infoArr[15],
        "ENGINE_0W1HW6" => $infoArr[16],
        "EXT_TTIJXI" => $infoArr[17],
        "INT_LMYJKX" => $infoArr[18],
        "KEY_XE1GGS" => $infoArr[19],
        "STATUS_4Y8DH6" => $infoArr[20],
        "LOCATION_AY82FS" => $infoArr[21],
        "LOCATION_1_X0WQ2A" => $infoArr[22],
        "COMMENT_WZIN0E" => $infoArr[23],
        "SALES_DATE_F6PP94" => $infoArr[24],
        "BUYER_Z673JR" => $infoArr[25],
        "SALES_INV_YHR5Z3" => $infoArr[26],
        "_OF_DAYS_3UH6SP" => $infoArr[27],
        "_OF_OVERSTAYED_DAYS__95GTR0" => $infoArr[28],
        
    );

    $arLoadProductArray = Array(
        "IBLOCK_ID" => $IBLOCK_ID,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => $infoArr[14],
        "ACTIVE"         => "Y",
        "IBLOCK_SECTION_ID" => 15,
        // "SECTION_ID" => 15,
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


$errors = array();
$json = array();

try {
    $json = \Bitrix\Main\Web\Json::decode(\Bitrix\Main\HttpRequest::getInput());
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}



$prods=$json;

$resArr=array();

foreach($prods as $prod){
    $res = addElement($prod);
    array_push($resArr,$res);
}


ob_end_clean();
echo json_encode($resArr, JSON_UNESCAPED_UNICODE);