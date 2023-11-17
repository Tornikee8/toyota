<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php");



?>

<script>

    
setInterval(() => {

    pathname = window.location.pathname.split("/");

    // console.log(pathname);

    if(pathname[1] == 'crm' && pathname[2] == 'deal' ){


        closeButtonForRefresh = document.querySelector('.side-panel-label-icon-box');
        if(closeButtonForRefresh){
            closeButtonForRefresh.addEventListener("click", () =>{
                // console.log('restart');
                setTimeout(() => {

                    if(pathname[3] == 'list' && pathname[2] == 'catalog' ){
                        // console.log('restart');
                        location.reload();
                    }      
                    
                }, 800);
            })
        }
                
        

    }





    if(pathname[3] == 'list' && pathname[2] == 'catalog' ){

        let garigebaCollNum = '';
        let idCollNum = '';
        let statusCollNum = '';
    

        showCreateDeal = "NO";

        let filter = document.getElementById('CrmProductGrid_search_container');









        for (let i = 0; i < filter.children.length; i++) {
                
            ///// excel შეტვირთვის ღილაკები /////

            if(filter.children[i].getAttribute("title") == 'Section: .Internal Stock'){

                let settingTraqtor =document.querySelector('.ui-toolbar-right-buttons');


                excelUploadButton = document.getElementById("excelUploadButton");

                if(excelUploadButton){

                    excelUploadButton.innerHTML=`<div  style="margin:15px;padding: 15px; background:green; font-size: 12px; width: 120px;">
                            <a style="color:white" href='http://213.131.35.178:62100/crm/deal/uploadexcelinternal.php'>Excel შეტვირთვა</a>
                        </div>`;

                }else{

                    excelisShetvirtva = document.createElement('div');
                    excelisShetvirtva.id='excelUploadButton';
                    excelisShetvirtva.innerHTML=`<div  style="margin:15px;padding: 15px; background:green; font-size: 12px; width: 120px;">
                            <a style="color:white" href='http://213.131.35.178:62100/crm/deal/uploadexcelinternal.php'>Excel შეტვირთვა</a>
                        </div>`;

                    settingTraqtor.appendChild(excelisShetvirtva);

                }


            }else if(filter.children[i].getAttribute("title") == 'Section: .კავკასიის საწყობი'){

                let settingTraqtor =document.querySelector('.ui-toolbar-right-buttons');

                excelUploadButton = document.getElementById("excelUploadButton");

                if(excelUploadButton){
                    excelUploadButton.innerHTML=`<div  style="margin:15px;padding: 15px; background:green; font-size: 12px; width: 120px;">
                            <a style="color:white" href='http://213.131.35.178:62100/crm/deal/uploadExcel.php'>Excel შეტვირთვა</a>
                        </div>`;
                }else{
                    excelUploadButton = document.createElement('div');
                    excelUploadButton.id='excelUploadButton';
                    excelUploadButton.innerHTML=`<div  style="margin:15px;padding: 15px; background:green; font-size: 12px; width: 120px;">
                            <a style="color:white" href='http://213.131.35.178:62100/crm/deal/uploadExcel.php'>Excel შეტვირთვა</a>
                        </div>`;

                        settingTraqtor.appendChild(excelUploadButton);
                }

            }else if (filter.children[i].getAttribute("title") != 'Section: .კავკასიის საწყობი' && filter.children[i].getAttribute("title") != 'Section: .Internal Stock' && filter.children[i].getAttribute("title")){
                excelUploadButton = document.getElementById("excelUploadButton");
                if(excelUploadButton){
                    excelUploadButton.innerHTML=``;
                }
            }



            if(filter.children[i].getAttribute("title") == 'Section: .Internal Stock' || filter.children[i].getAttribute("title") ==  'Section: .კავკასიის საწყობი' || filter.children[i].getAttribute("title") ==  'Section: .მეორადი ავტოები'){
                showCreateDeal = "YES";
            }
        }

        if(showCreateDeal == "YES"){
            let tableHeader=document.querySelector('.main-grid-header');
            if(tableHeader){
                tableNames = tableHeader.children[0].children;
                for (let i = 0; i < tableNames.length; i++) {
                    dataName=tableNames[i].getAttribute("data-name");
                    if(dataName == 'PROPERTY_101'){
                        garigebaCollNum = i ;
                    }  
                    if(dataName == 'ID'){
                        idCollNum = i ;
                    }
                    if(dataName == 'PROPERTY_100'){
                        statusCollNum = i ;
                    }

                }
            }

            let tableBody =document.querySelector('.main-grid-table');

            if(tableBody){
                colums=tableBody.children[1].children;
                for (let j = 0; j < colums.length; j++) {

                    dealLink = colums[j].children[garigebaCollNum].children[0].children[0].children[0];

                    prodID = colums[j].children[idCollNum].children[0].children[0].textContent;

                    dealLinkParent = colums[j].children[garigebaCollNum].children[0].children[0];

                    statusValue = colums[j].children[statusCollNum].children[0].children[0].textContent;

                    if(statusValue == 'დაჯავშნილი' ){
                        
                        for (let i = 0; i < colums[j].children.length; i++) {
                            colums[j].children[i].style.background='yellow';                            
                        }

                    }else if(statusValue == 'კალკულაციის მოლოდინში'){

                        for (let i = 0; i < colums[j].children.length; i++) {
                            colums[j].children[i].style.background='orange';                            
                        }
                    }

                    if(dealLink?.children?.length){
                        
                    }else{
                        dealLinkParent.innerHTML=`<a href='http://213.131.35.178:62100/crm/deal/details/0/?category_id=0&UF_CRM_1698932727115=${prodID}'>გარიგების შექმნა</a>`;
                    }
                    
                }
            }      
        } 

    }

}, 1000);





</script>