    <?

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
    $APPLICATION->SetTitle("upload CSV");



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


    foreach ($arParams["CSV"] as $entry) {


        if($entry[8]){
            $arFilter=array("IBLOCK_ID" => 14,
                            'PROPERTY_CHASSIS_X0IZII' => $entry[8]);
            $registeredProd=getCIBlockElementsByFilter($arFilter);

            if(!$registeredProd && $entry[8] != "CHASSIS") {
                $entry['vendor']='';
                array_push($registeredProds['new'] , $entry);
            }elseif($registeredProd){
                $entry['vendor']='კავკასიის საწყობი';
                array_push($registeredProds['good'] , $entry);
            }
        }

        
    }




    if($registeredProds['good']){

    

        ?> 
        

        <table class="contentTable" id="contentTable">
            <thead class="contentTableHead">
                <tr >
                    <td>CHASSIS</td>
                    <td>INVOICE #</td>
                    <td>PROD. MON</td>
                    <td>STOCKING DATE</td>
                    <td>VEHICLE</td>
                    <td>ENGINE</td>
                    <td>MODEL CODE</td>
                    <td>SFX</td>
                    <td>SALE PRICE</td>
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
    let content = "";






    if(prodArr['new']){
        for(let i = 0; i < prodArr['new'].length; i++){
            content += `<tr id='NEW_Prod'>
            <td class="CHASSIS_NEW">${prodArr['new'][i][8]}</td>
            <td class="CHASSIS_NEW">${prodArr['new'][i][1]}</td> 
            <td class="CHASSIS_NEW">${prodArr['new'][i][2]}</td>  
            <td class="CHASSIS_NEW">${prodArr['new'][i][3]}</td> 
            <td class="CHASSIS_NEW">${prodArr['new'][i][4]}</td> 
            <td class="CHASSIS_NEW">${prodArr['new'][i][5]}</td> 
            <td class="CHASSIS_NEW">${prodArr['new'][i][6]}</td> 
            <td class="CHASSIS_NEW">${prodArr['new'][i][7]}</td> 
            <td class="CHASSIS_NEW">${prodArr['new'][i][14]}</td> 
            <td class="NEW_VENDOR">  <p> <input id='${prodArr['new'][i][8]}' type="text"><label></label></p></td>
    `
            
        }
    }



    if(prodArr['good']){
        
        for(let i = 0; i < prodArr['good'].length; i++){

            content += `<tr>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][8]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][1]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][2]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][3]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][4]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][5]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][6]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][7]}</td>
                            <td class="CHASSIS_GOOD" >${prodArr['good'][i][14]}</td>
                        </tr>`
        }
    }

    let createbtn=`<button onClick="createProds();" id="createBtn">დამატება</button>`;

    if(document.getElementById("prodTableContent")){                
         document.getElementById("prodTableContent").innerHTML = content;
    }
    if(document.getElementById("createDiv")){
        document.getElementById("createDiv").innerHTML = createbtn;
    }

    









    function createProds() {

    prods={};

    prods['old'] = prodArr['good'];

    prods['new'] = prodArr['new'];

    prods['new'].forEach(element => {
            shassID = element[8];
            element['vendor'] = document.getElementById(shassID).value;
    });



        post_fetch(`${location.origin}/crm/deal/movetointernalstock.php`, prods)
            .then(data => {
                return data.json();
            })
            .then(data => {
                console.log(data);
            })
            .catch(err => {
                console.log(err);
        });


        location.href='http://213.131.35.178:62100/crm/deal/uploadexcelinternal.php';

    }


    async function post_fetch(url, data = {}) {
        console.log(data);
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


