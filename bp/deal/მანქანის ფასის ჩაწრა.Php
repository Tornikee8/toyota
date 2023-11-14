<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");


function printArr($arr) {
    echo "<pre>"; print_r($arr); echo "</pre>";
}



$root=$this->GetRootActivity();
$deal_ID=$root->GetVariable("deal_ID");
$price=$root->GetVariable("price");



$dealPrice = explode("|",$price);

$priceNum = $dealPrice[0];
$valuta = $dealPrice[1];




$CCrmDeal = new CCrmDeal();
$upd = array(
    "OPPORTUNITY" => $priceNum,
    "ACCOUNT_CURRENCY_ID" => $valuta,
    "CURRENCY_ID" => $valuta,
    

);
$CCrmDeal->Update($deal_ID, $upd);