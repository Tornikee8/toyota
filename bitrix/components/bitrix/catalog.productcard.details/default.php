<?php


CJSCore::Init(array("jquery"));
/**
 * @global \CMain $APPLICATION
 * @var $component \CatalogProductDetailsComponent
 * @var $this \CBitrixComponentTemplate
 * @var array $arResult
 * @var array $arParams
 *
 * @var string $templateFolder
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main\Application;
use Bitrix\Main\IO\File;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Json;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;
use Bitrix\UI\Buttons\JsHandler;
use Bitrix\UI\Buttons\SettingsButton;
use Bitrix\UI\Toolbar\Facade\Toolbar;

$bodyClass = $APPLICATION->GetPageProperty("BodyClass");
$APPLICATION->SetPageProperty('BodyClass', ($bodyClass ? $bodyClass.' ' : '').'no-background');

Loader::includeModule('ui');

$createDocumentButtonId = null;
if (isset($arResult['CREATE_DOCUMENT_BUTTON']))
{
	$createDocumentButton = new \Bitrix\UI\Buttons\Split\Button($arResult['CREATE_DOCUMENT_BUTTON']['PARAMS']);
	Toolbar::addButton($createDocumentButton);
	$createDocumentButtonId = $createDocumentButton->getUniqId();
}

$settingsButton = new SettingsButton([
	'className' => $arResult['IS_NEW_PRODUCT'] ? 'ui-btn-highlighted' : '',
]);
Toolbar::addButton($settingsButton);

$feedbackButton = new Button([
	'color' => Color::LIGHT_BORDER,
	'text' => Loc::getMessage('CPD_FEEDBACK_BUTTON'),
	'className' => $arResult['IS_NEW_PRODUCT'] ? 'ui-btn-highlighted' : '',
	'onclick' => new JsHandler(
		'BX.Catalog.ProductCard.Instance.openFeedbackPanel',
		'BX.Catalog.ProductCard.Instance'
	),
]);
$feedbackButton->addDataAttribute('toolbar-collapsed-icon', Icon::ADD);
Toolbar::addButton($feedbackButton);

Toolbar::deleteFavoriteStar();

Extension::load([
	'catalog.entity-card',
	'admin_interface',
	'sidepanel',
	'ui.hint',
]);

$tabs = [];

if ($arResult['TAB_LIST']['MAIN'])
{
	$tabs[] = [
		'id' => 'main',
		'name' => Loc::getMessage('CPD_TAB_GENERAL_TITLE'),
		'enabled' => true,
		'active' => true,
	];
}
if ($arResult['TAB_LIST']['BALANCE'])
{
	$tabs[] = [
		'id' => 'balance',
		'name' => Loc::getMessage('CPD_TAB_BALANCE_TITLE'),
		'enabled' => !$arResult['IS_NEW_PRODUCT'],
		'active' => false,
	];
}
if ($arResult['TAB_LIST']['SEO'])
{
	$tabs[] = [
		'id' => 'seo',
		'name' => 'SEO',
		'enabled' => false,
		'active' => false,
	];
}

$guid = 'product-details';
$containerId = "{$guid}_container";
$tabMenuContainerId = "{$guid}_tabs_menu";
$tabContainerId = "{$guid}_tabs";

$cardParameters = [
	'entityId' => $arResult['PRODUCT_FIELDS']['ID'],
	'componentName' => $component->getName(),
	'componentSignedParams' => $component->getSignedParameters(),
	'variationGridComponentName' => $arResult['VARIATION_GRID_COMPONENT_NAME'],
	'isSimpleProduct' => $arResult['SIMPLE_PRODUCT'],
	'tabs' => $tabs,
	'settingsButtonId' => $settingsButton->getUniqId(),
	'cardSettings' => $arResult['CARD_SETTINGS'],
	'hiddenFields' => $arResult['HIDDEN_FIELDS'],
	'isWithOrdersMode' => $arResult['IS_WITH_ORDERS_MODE'],
	'isInventoryManagementUsed' => $arResult['IS_INVENTORY_MANAGEMENT_USED'],
	'createDocumentButtonId' => $createDocumentButtonId,
	'createDocumentButtonMenuPopupItems' => $arResult['CREATE_DOCUMENT_BUTTON']['POPUP_ITEMS'] ?? [],
	'feedbackUrl' => $arParams['PATH_TO']['FEEDBACK'] ?? '',
	'containerId' => $containerId,
	'tabContainerId' => $tabContainerId,
	'tabMenuContainerId' => $tabMenuContainerId,
	'creationPropertyUrl' => $arResult['UI_CREATION_PROPERTY_URL'],
	'creationVariationPropertyUrl' => $arResult['UI_CREATION_SKU_PROPERTY_URL'],
	'variationGridId' => $arResult['VARIATION_GRID_ID'],
	'productStoreGridId' => $arResult['STORE_AMOUNT_GRID_ID'],
	'productTypeSelector' => 'catalog-productcard-product-type-selector',
	'productTypeSelectorTypes' => $arResult['DROPDOWN_TYPES'],
];
?>
<script>
	BX.message(<?=Json::encode(Loc::loadLanguageFile(__FILE__))?>);
	BX(function() {
		let topWindow = BX.PageObject.getRootWindow().window
		if (!topWindow.adminSidePanel || !BX.is_subclass_of(topWindow.adminSidePanel, BX.adminSidePanel))
		{
			topWindow.adminSidePanel = new BX.adminSidePanel({
				publicMode: true
			});
		}

		BX.Catalog.ProductCard.Instance = new BX.Catalog.ProductCard(
			'<?=CUtil::JSEscape($guid)?>',
			<?= CUtil::PhpToJSObject($cardParameters) ?>
		);
	});
</script>
<?php
if (!empty($arResult['DROPDOWN_TYPES']))
{
	$dropDownTypes = '<div id="catalog-productcard-product-type-selector" class="catalog-productcard-product-type-selector">'
		. '<span class="catalog-productcard-product-type-selector-text" data-hint="" data-hint-no-icon>'
		. Loc::getMessage('CPD_PRODUCT_TYPE_SELECTOR', ['#PRODUCT_TYPE_NAME#' => $arResult['PRODUCT_TYPE_NAME']])
		. '</span>'
		. '</div>'
	;
	Toolbar::addUnderTitleHtml($dropDownTypes);
	unset($dropDownTypes);
}
?>
<div id="<?=htmlspecialcharsbx($containerId)?>" class="catalog-entity-wrap catalog-wrapper">
	<?php
	$tabContainerClassName = 'catalog-entity-section catalog-entity-section-tabs';
	$tabContainerClassName .= ' ui-entity-stream-section-planned-above-overlay';
	?>
	<div class="<?=$tabContainerClassName?>">
		<ul id="<?=htmlspecialcharsbx($tabMenuContainerId)?>" class="catalog-entity-section-tabs-container">
			<?php
			foreach ($tabs as $tab)
			{
				$classNames = ['catalog-entity-section-tab'];

				if (isset($tab['active']) && $tab['active'])
				{
					$classNames[] = 'catalog-entity-section-tab-current';
				}
				elseif (isset($tab['enabled']) && !$tab['enabled'])
				{
					$classNames[] = 'catalog-entity-section-tab-disabled';
				}
				?>
				<li data-tab-id="<?=htmlspecialcharsbx($tab['id'])?>" class="<?=implode(' ', $classNames)?>">
					<a class="catalog-entity-section-tab-link" href="#"><?=htmlspecialcharsbx($tab['name'])?></a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
	<div id="<?=htmlspecialcharsbx($tabContainerId)?>" style="position: relative;">
		<?php
		foreach ($tabs as $tab)
		{
			$tabId = $tab['id'];
			$className = 'catalog-entity-section catalog-entity-section-info';
			$style = '';

			if ($arResult['IS_NEW_PRODUCT'])
			{
				$className .= ' catalog-entity-section-new';
			}

			if (!$tab['active'])
			{
				$className .= ' catalog-entity-section-tab-content-hide catalog-entity-section-above-overlay';
				$style = 'style="display: none;"';
			}
			?>
			<div data-tab-id="<?=htmlspecialcharsbx($tabId)?>" class="<?=$className?>" <?=$style?>>
				<?php
				$tabFolderPath = Application::getDocumentRoot() . $templateFolder . '/tabs/';
				$file = new File($tabFolderPath.$tabId . '.php');

				if ($file->isExists())
				{
					include $file->getPath();
				}
				else
				{
					echo 'Unknown tab {' . $tabId . '}.';
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
</div>

<script>




setInterval(() => {

pathname 	= window.location.pathname.split("/");

if(pathname[2] == "catalog" && pathname[3] == "14" && pathname[4] == "product"){



    let variantDivContainer=document.querySelector('[data-name="variation_grid"]');
    
    if(variantDivContainer){
        variantDivContainer.style.display="none";
    }
	

	
    let iblocSectionName=document.querySelector('[data-cid="IBLOCK_SECTION"]');
    
 

    let settingGilaki=document.querySelectorAll('.ui-toolbar-right-buttons');


	if(iblocSectionName){
        iblocSectionName.style.pointerEvents="none";

        sectionFilter=iblocSectionName.children[2].children[0].children[0].children[0].textContent;

        if(sectionFilter == "კავკასიის საწყობი"){
            
			if(!document.getElementById("sectionChangeButton")){
				sectionChange = document.createElement('div');
				sectionChange.id='sectionChangeButton';

				sectionChange.innerHTML=`<div  onclick="showProdChange();" class="webform-small-button webform-small-button-transparent">საწყობის ცვლილება</div>`;

				settingGilaki[0].parentElement.appendChild(sectionChange);
			}
        }  
    }
    
    


    if(settingGilaki){
        for (let i = 0; i < settingGilaki.length; i++) {   


            if(settingGilaki[i].getAttribute("id")!="sectionChangeButton"){
                settingGilaki[i].style.display="none";
            }
        }
    }

  


    let inventoryButton=document.querySelector('[data-tab-id="balance"]');

    if(inventoryButton){
        inventoryButton.style.display="none";
    }


}


}, 1000);









let prodChangeContanier = `
<div id="prodChangeContanier" style="position: absolute; left: 25%; top: 15%; z-index: 1000; width: 600px; padding: 0 20px 0; background-color: #fff;">
		<div style="height: 49px;">
			<span class="popup-window-titlebar-text">საწყობის ცვლილება</span>
		</div>
		<div style="overflow-x: auto; padding: 20px; background-color: #eef2f4;">
			<div>
				<div class="bizproc-item bizproc-workflow-template" style="border: 1px solid #D8D8D8; padding: 0 1.5em 1.5em 1em;">
					<span class="bizproc-item-legend bizproc-workflow-template-title" style="padding: 0 1em; margin-left: 2em; font-size: 110%; color: #000000; position: absolute; top: 61px; background: #eef2f4;">საწყობის ცვლილება</span>
					<div class="bizproc-modern-type-control-container" style="margin: 10px 0 17px 0; position: relative;">
						
					<div   id="priceDiv" class="bizproc-modern-type-control-container" style="margin: 10px 0 17px 0; position: relative;">
						<span style="display: block; margin: 0 0 15px 0; font-size: 13px; color: #80868e;">
							ფასი:
						</span>
						<div>
							<input id="priceValue" class="bizproc-type-control bizproc-type-control-double" style="width: 100%; height: 36px;" type="text" />
						</div>
					</div>	
					<div   id="priceDivGel" class="bizproc-modern-type-control-container" style="margin: 10px 0 17px 0; position: relative;">
						<span style="display: block; margin: 0 0 15px 0; font-size: 13px; color: #80868e;">
							ფასი (ლარი):
						</span>
						<div>
							<input id="priceValueGel" class="bizproc-type-control bizproc-type-control-double" style="width: 100%; height: 36px;" type="text" />
						</div>
					</div>	
					</div>
				</div>
			</div>
			<div id="prodChangeSuccessBlock" style="display: none; color: green; text-align: center; margin: 15px 0 5px; font-size: 16px;">თქვენი მოთხოვნა გაგზავნილია</div>
			<div id="prodChangeFailBlock" style="display: none; color: red; text-align: center; margin: 15px 0 5px; font-size: 16px;">მოთხვონის დროს დაფიქსირდა შეცდომა</div>
			<div id="prodChangeWarningBlock" style="display: none; color: red; text-align: center; margin: 15px 0 5px; font-size: 16px;">გთხოვთ შეავსოთ ყველა ველი</div>
		</div>
		<span onclick="removeProdChange();" class="popup-window-close-icon popup-window-titlebar-close-icon"></span>
		<div style="text-align: center; padding: 20px 0 10px; position: relative;">
			<span id="saveProdChangeBtn" onclick="saveProdChange()" class="popup-window-button" style="background: #bbed21;-webkit-box-shadow: none; box-shadow: none; color: #535c69;">შენახვა</span>
			<span onclick="removeProdChange();" class="popup-window-button" style="margin-right: 0; color: #f1361b; border-bottom-color: #ffb4a9">გაუქმება</span>
		</div>
	</div>
`;





function showProdChange() {
	let templateContainer = document.querySelector(".template-bitrix24");
	$(templateContainer).append(prodChangeContanier);      
}


function removeProdChange() {
	let prodChangeContanier = document.getElementById("prodChangeContanier");
	let templateContainer = document.querySelector(".template-bitrix24");

	templateContainer.removeChild(prodChangeContanier);
}


function saveProdChange() {
          
	let price = document.getElementById("priceValue").value;
	let priceGel = document.getElementById("priceValueGel").value;

	if(!price || !priceGel){
		let error = document.getElementById("prodChangeWarningBlock").style.display="block";
	}


	if(price && priceGel) {

		let params = {};

		params["price"] = price;
		params["priceGel"] = priceGel;
		params["prodId"]=pathname[5];
		

		console.log(params);
	
		post_fetch(`${location.origin}/rest/local/changeCategory.php`, {"params":params})
        .then(data => {
            return data.json();
        })
        .then(data => {
            console.log(data);
        })
        .catch(err => {
            console.log(err);
   		 });
		 

		location.href='http://213.131.35.178:62100/crm/catalog/list/16/?IBLOCK_ID=14';
		
	}
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
