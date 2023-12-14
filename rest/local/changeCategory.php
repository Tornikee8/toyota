<?
ob_start();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("createProd");


function getCIBlockElementsByFilter($arFilter = array(),$sort = array()){
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


$errors = array();
$json = array();

try {
    $json = \Bitrix\Main\Web\Json::decode(\Bitrix\Main\HttpRequest::getInput());
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}


$params=$json["params"];



$ProductID=$params['prodId'];

$arFilter=array("ID"=>$ProductID);
$Product=getCIBlockElementsByFilter($arFilter);
if($Product[0]['ID']){
    
    $Product[0]["VENDOR_BWFR1K"] = "კავკასიის საწყობი";

    
    if($Product[0]["deal_status"] == "დაჯავშნილი"){
        $Product[0]["deal_status"] = 87;
    }elseif($Product[0]["deal_status"] == "კალკულაციის მოლოდინში"){
        $Product[0]["deal_status"] = 87;  //   88 იყო კალკულაციის მოლოდინი და internal stock-ში კალკულაციის მოლოდინი არ უნდა იყოს

        $deal_ID = $Product[0]["DEAL"]



        $CCrmDeal = new CCrmDeal();
        $upd = array(
            "STAGE_ID" => "UC_DPD51C",   // დაჯავშნულზე გადატანა
        );
        $CCrmDeal->Update($deal_ID, $upd);

    }
    elseif($Product[0]["deal_status"] == "დაინტერესებული"){
        $Product[0]["deal_status"] = 90;
    }
      


    $arLoadProductArray = array(
        "PROPERTY_VALUES" => $Product[0],
        "NAME" => $Product[0]["NAME"],
        "IBLOCK_SECTION_ID" => 16,
        "ACTIVE" => "Y",
    );



    $el = new CIBlockElement;
    $res = $el->Update($ProductID, $arLoadProductArray);

        
    




    $response=$json;
}



ob_end_clean();
echo json_encode($Product, JSON_UNESCAPED_UNICODE);