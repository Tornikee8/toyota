<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("test");

function printArr($arr) {
    echo "<pre>"; print_r($arr); echo "</pre>";
}



function getContactsByFilter ($id) {
    $res = CCrmContact::GetList(array("ID" => "ASC"), array("ID"=>$id), array());
    $contacts=array();
    if($arContact = $res->Fetch()){
        array_push($contacts, $arContact);
    }

    return $contacts;
}



function getDealsByFilter_18($arFilter, $arSelect = array(), $arSort = array("ID"=>"DESC")) {
    $arDeals = array();
    $res = CCrmDeal::GetList($arSort, $arFilter, array("ID","STAGE_ID","CONTACT_ID"));
    while($arDeal = $res->Fetch()) array_push($arDeals, $arDeal);
    return (count($arDeals) > 0) ? $arDeals : false;
}





$root=$this->GetRootActivity();
$CONTACT_ID=$root->GetVariable("CONTACT_ID");

// $CONTACT_ID= 12;


$contact=getContactsByFilter($CONTACT_ID);



$dbFieldMulti = \CCrmFieldMulti::GetList(array(), array('ENTITY_ID' => 'CONTACT','TYPE_ID' => 'PHONE', 'VALUE_TYPE' => 'MOBILE|WORK', "ELEMENT_ID" => $CONTACT_ID))->Fetch();

$phoneNum=$dbFieldMulti["VALUE"];


$dbFieldMulti = \CCrmFieldMulti::GetList(array(), array('ENTITY_ID' => 'CONTACT','TYPE_ID' => 'EMAIL', 'VALUE_TYPE' => 'EMAIL|WORK', "ELEMENT_ID" => $CONTACT_ID))->Fetch();

$mail=$dbFieldMulti["VALUE"];

$adress = $contact[0]["UF_CRM_1700224206840"];
$pn = $contact[0]["UF_CRM_1700224199809"];



$arFilter=array("CONTACT_ID"=>$CONTACT_ID);
$deals=getDealsByFilter_18($arFilter);

foreach($deals as $deal){

    $deal_ID=$deal["ID"];


    
    $CCrmDeal = new CCrmDeal();
    $upd = array(
        "UF_CRM_1700224481829" => $phoneNum,
        "UF_CRM_1700223571959" => $mail,
        "UF_CRM_1700223543335" => $adress,
        "UF_CRM_1700223522193" => $pn,
    );
    $CCrmDeal->Update($deal_ID, $upd);

}

