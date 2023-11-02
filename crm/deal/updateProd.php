<?
ob_start();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("createProd");


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

foreach($prods as $prod){
    
    if($prod['type']=='move'){

        $chassis=$prod['id'];

        $arFilter = array(
            "PROPERTY_CHASSIS_X0IZII" => $chassis,
        );
        
        $Product = getCIBlockElementsByFilter($arFilter);

        $ProductID=$Product[0]['ID'];

       
    
        $arLoadProductArray = array(
            "PROPERTY_VALUES" => $Product[0],
            "NAME" => $Product[0]["NAME"],
            "IBLOCK_SECTION_ID" => 17,
            "ACTIVE" => "Y",
        );
        

        
        $el = new CIBlockElement;
        $res = $el->Update($ProductID, $arLoadProductArray);
        

        array_push($resArr,$ProductID);


    }elseif($prod['type']=='moveBack'){

        $chassis=$prod['id'];

        $arFilter = array(
            "PROPERTY_CHASSIS_X0IZII" => $chassis,
        );
        
        $Product = getCIBlockElementsByFilter($arFilter);

        $ProductID=$Product[0]['ID'];

        $arLoadProductArray = array(
            "PROPERTY_VALUES" => $Product[0],
            "NAME" => $Product[0]["NAME"],
            "IBLOCK_SECTION_ID" => 15,
            "ACTIVE" => "Y",
        );
        

        
        $el = new CIBlockElement;
        $res = $el->Update($ProductID, $arLoadProductArray);
        

        array_push($resArr,$ProductID);


    }

}


ob_end_clean();
echo json_encode($resArr, JSON_UNESCAPED_UNICODE);