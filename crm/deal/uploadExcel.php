<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("upload CSV");

function printArr($arr) {
    echo "<pre>"; print_r($arr); echo "</pre>";
}



CJSCore::Init(array("jquery"));
function issetEx($param) { return (isset($param) && !empty($param)) ? $param : false; }

function getCIBlockElementsByFilter($arFilter = array()) {
    $arElements = array();
    $arSelect = Array("ID","NAME","TIMESTAMP_X","PROPERTY_CHASSIS_X0IZII","PROPERTY_PROD_MON_Z1ICNY","PROPERTY_SHIP_DATE_TA6TRF","PROPERTY_STOCKING_DATE_LCAFN6","PROPERTY_VEHICLE_VZMJ9H","PROPERTY_ENGINE_1BWQM4","PROPERTY_MODEL_CODE_6HLW6F","PROPERTY_SFX_CT8L4X");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>5000), $arSelect);
    while($ob = $res->GetNextElement()) {
        $arFilds = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $arPushs = array();
        foreach($arFilds as $key => $arFild) $arPushs[$key] = $arFild;
        foreach($arProps as $key => $arProp) $arPushs[$key] = $arProp["VALUE"];
    //     $price      = CPrice::GetBasePrice($arPushs["ID"]);
    //    $arPushs["PRICE"] = $price["PRICE"];
        array_push($arElements, $arPushs);
    }
    return $arElements;
}

function removeExtraSpaces($txt) {
    $txt = preg_replace('/\s+/', ' ', $txt);
    
    return $txt;
}

function strToNum($extranetId) {
    $new = "";
    $extranetId = str_split($extranetId);
    $digits = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    foreach ($extranetId as $char) {
        if (in_array($char, $digits)) $new .= $char;
    }
    return $new;
}


function getAllProds(){

    $res = array();
    $arFilter=Array("IBLOCK_ID"=>14,"IBLOCK_SECTION_ID" => 15);
    $prods=getCIBlockElementsByFilter($arFilter);

   
    return $prods;

}


function getAllProdsLost(){

    $res = array();
    $arFilter=Array("IBLOCK_ID"=>14,"IBLOCK_SECTION_ID" => 17);
    $prods=getCIBlockElementsByFilter($arFilter);


    return $prods;

}

// -------------------------------------------------- //

$arParams = array();
$arParams['CSV'] = array();
//
if(issetEx($_FILES)) {
//     echo "<pre>"; print_r($_POST); echo "</pre>";
    $row = 0;
    if(($handle = fopen($_FILES['csv']['tmp_name'] ,"r")) !== FALSE) {
        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            for ($c=0; $c < $num; $c++) {
                $arParams['CSV'][$row][$c] = $data[$c];
            }
            $row++;
        }
        fclose($handle);
    }
}


$registeredProds=array();
$registeredProds['good']=array();
$registeredProds['new']=array();
$registeredProds['removed']=array();
$registeredProds['restore']=array();
$newChassis=array();
$goodProds=array();

foreach ($arParams["CSV"] as $entry) {

    // printArr($entry);

    if($entry[14]){

        array_push($newChassis,$entry[14]);

        $arFilter=array("IBLOCK_ID" => 14,
                        'PROPERTY_CHASSIS_X0IZII' => $entry[14]);
        $registeredProd=getCIBlockElementsByFilter($arFilter);

        if(!$registeredProd && $entry[14] != "CHASSIS") {
            array_push($goodProds , $entry);
        }elseif($registeredProd){
            array_push($registeredProds['good'] , $entry);
        }
    }

    
       
}



$oldProds=getAllProds();
$lostProds=getAllProdsLost();


$dealIsCreatedOnThisProd=array();


foreach($oldProds as $oldProd){
    if(!in_array($oldProd['PROPERTY_CHASSIS_X0IZII_VALUE'],$newChassis)){
        array_push($registeredProds['removed'],$oldProd);

        // printArr($oldProd);



    }
}

foreach($lostProds as $lostProd){
    if(in_array($lostProd['PROPERTY_CHASSIS_X0IZII_VALUE'],$newChassis)){
        array_push($registeredProds['restore'],$lostProd);
    }
}


if($registeredProds['good'] || $goodProds){

 

    ?> 
    

    <table class="contentTable" id="contentTable">
        <thead class="contentTableHead">
            <tr >
                <td>CHASSIS</td>
                <td>PROD. MON</td>
                <td>SHIP DATE</td>
                <td>STOCKING DATE</td>
                <td>VEHICLE</td>
                <td>ENGINE</td>
                <td>MODEL CODE</td>
                <td>SFX</td>
            </tr>
        </thead>
        <tbody id="prodTableContent" class="contentProds">

        </tbody>
    </table>

  


    <div class="createDiv" id="createDiv">
    
    </div>

    <?php 
    
}else{
    ?>
    <div align="center">
        <form action="" method="post" enctype="multipart/form-data">
            <table class="formTable">
                <tr>
                    <td><label>ფაილის ატვირთვა (მხოლოდ .csv ფორმატის)</label></td>
                    <td><input type="file" name="csv" value="csv"></td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <input type="submit" name="" value="ატვირთვა">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <table id="content" class="excelTable"></table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
}


?>

    <style>

        .formTable {
            border: 1px solid grey;
            border-collapse: collapse;
        }
        .formTable tr td {
            border: 1px solid grey;
            border-collapse: collapse;
            padding: 5px;
        }

        .excelTable {
            border: 1px solid #ddd;
            border-collapse: collapse;
        }
        .excelTable tr td {
            border: 1px solid #888;
            border-collapse: collapse;
            padding: 5px;
        }
        .excelTable tr td:first-child {
            background: #616161;
        }

        .contentTable{
            width:100%;
        }

        .CHASSIS_NEW{
            color:#05e905;
        }
        .CHASSIS_GOOD{
            
        }
        .CHASSIS_restore{
            color:blue;
        }
        .CHASSIS_removed{
            color:#FF0400;
        }
        
        .createDiv{
            width:100%;
            display:flex;
            justify-content:center;
        }
        .createBtn{
            height:50px;
        }

    </style>



<script>





let prodArr = <?php echo json_encode($registeredProds); ?>;
let newProds = <?php echo json_encode($goodProds); ?>;
let content = "";






if(prodArr['restore']){
    for(let i = 0; i < prodArr['restore'].length; i++){

        content += `<tr>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_CHASSIS_X0IZII_VALUE']}</td>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_PROD_MON_Z1ICNY_VALUE']}</td>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_SHIP_DATE_TA6TRF_VALUE']}</td>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_STOCKING_DATE_LCAFN6_VALUE']}</td>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_VEHICLE_VZMJ9H_VALUE']}</td>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_ENGINE_1BWQM4_VALUE']}</td>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_MODEL_CODE_6HLW6F_VALUE']}</td>
                            <td class="CHASSIS_restore">${prodArr['restore'][i]['PROPERTY_SFX_CT8L4X_VALUE']}</td>      
                    </tr>`

    }
}

if(prodArr['removed']){
    
    for(let i = 0; i < prodArr['removed'].length; i++){

        content += `<tr>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_CHASSIS_X0IZII_VALUE']}</td>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_PROD_MON_Z1ICNY_VALUE']}</td>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_SHIP_DATE_TA6TRF_VALUE']}</td>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_STOCKING_DATE_LCAFN6_VALUE']}</td>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_VEHICLE_VZMJ9H_VALUE']}</td>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_ENGINE_1BWQM4_VALUE']}</td>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_MODEL_CODE_6HLW6F_VALUE']}</td>
                            <td class="CHASSIS_removed">${prodArr['removed'][i]['PROPERTY_SFX_CT8L4X_VALUE']}</td>                            
                        </tr>`

    }
}


if(newProds){
    for(let i = 0; i < newProds.length; i++){
        content += `<tr id='NEW_Prod'>
        <td class="CHASSIS_NEW">${newProds[i][14]}</td>
        <td class="CHASSIS_NEW">${newProds[i][2]}</td> 
        <td class="CHASSIS_NEW">${newProds[i][3]}</td>  
        <td class="CHASSIS_NEW">${newProds[i][6]}</td> 
        <td class="CHASSIS_NEW">${newProds[i][7]}</td> 
        <td class="CHASSIS_NEW">${newProds[i][8]}</td> 
        <td class="CHASSIS_NEW">${newProds[i][9]}</td> 
        <td class="CHASSIS_NEW">${newProds[i][12]}</td> 
 `
        
    }
}



if(prodArr['good']){
    
    for(let i = 0; i < prodArr['good'].length; i++){

        content += `<tr>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["14"]}</td>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["2"]}</td>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["3"]}</td>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["6"]}</td>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["7"]}</td>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["8"]}</td>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["9"]}</td>
                        <td class="CHASSIS_GOOD" >${prodArr['good'][i]["12"]}</td>
                    </tr>`
    }
}

let createbtn=`<button onClick="createProds();" id="createBtn">დამატება</button>`;


prodTableContent = document.getElementById("prodTableContent")
if(prodTableContent){
    document.getElementById("prodTableContent").innerHTML = content;
}

createDiv = document.getElementById("createDiv")

if(createDiv){
    document.getElementById("createDiv").innerHTML = createbtn;
}
    










function createProds() {

    oldProds=[];


    for(let i = 0; i < prodArr['removed'].length; i++){      
        data={ 'id':prodArr['removed'][i]['PROPERTY_CHASSIS_X0IZII_VALUE'],
            'type':'move'
        };
        oldProds.push(data);
    }


    for(let i = 0; i < prodArr['restore'].length; i++){      
        data={ 'id':prodArr['restore'][i]['PROPERTY_CHASSIS_X0IZII_VALUE'],
            'type':'moveBack'
        };
        oldProds.push(data);
    }



   
    
    post_fetch(`${location.origin}/crm/deal/createProd.php`, newProds)
        .then(data => {
            return data.json();
        })
        .then(data => {
            console.log(data);
        })
        .catch(err => {
            console.log(err);
    });

    

    post_fetch(`${location.origin}/crm/deal/updateProd.php`, oldProds)
        .then(data => {
            return data.json();
        })
        .then(data => {
            console.log(data);
        })
        .catch(err => {
            console.log(err);
    });

    

    location.href='http://213.131.35.178:62100/crm/deal/uploadExcel.php';

}


async function post_fetch(url, data = {}) {
        const response = await fetch(url, {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
            },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify(data)
        });
        return response;
    }


</script>





<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");

?>


