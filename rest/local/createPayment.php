<?
ob_start();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("createProd");



function addElement($infoArr) {
    $IBLOCK_ID = 17;
    $el = new CIBlockElement();
    // printArr($infoArr);
 

    $PROP = array(
        "DATE" => $infoArr["paymentDate"],
        "MONEY" => $infoArr["paymentValue"],
        "DEAL" => $infoArr["dealID"],
  
    );

    $arLoadProductArray = Array(
        "IBLOCK_ID" => $IBLOCK_ID,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => "გადახდა",
        "ACTIVE"         => "Y",
    );
  
    $res = $el->Add($arLoadProductArray);
    

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


$params=$json["params"];


$newPayment=addElement($params);






ob_end_clean();
echo json_encode($newPayment, JSON_UNESCAPED_UNICODE);