<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
CJSCore::Init(array("jquery"));
/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CDatabase $DB
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 */

use Bitrix\Crm;
use Bitrix\Main;

Main\UI\Extension::load("ui.label");
Main\UI\Extension::load("crm.entity-editor");
Main\UI\Extension::load("crm.entity-editor.field.requisite");
Main\UI\Extension::load("ui.icons.b24");

Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/crm/activity.js');

if(Main\Loader::includeModule('calendar'))
{
	\Bitrix\Crm\Integration\Calendar::loadResourcebookingUserfieldExtention();
}

$guid = $arResult['GUID'];
$prefix = mb_strtolower($guid);
$containerID = "{$prefix}_container";
$buttonContainerID = "{$prefix}_buttons";
$createSectionButtonID = "{$prefix}_create_section";
$configMenuButtonID = '';
$configIconID = '';



//// ================================== ჩემი ჩამატებული ===============================///////////

function getDealsByFilter_CRM_ENTITY($arFilter, $arSelect = array(), $arSort = array("ID"=>"DESC")) {
    $arDeals = array();
	$arSelect=array("ID","CURRENCY_ID","OPPORTUNITY","STAGE_ID","UF_CRM_1701270482886","UF_CRM_1701416970265");
    $res = CCrmDeal::GetList($arSort, $arFilter, $arSelect);
    while($arDeal = $res->Fetch()) array_push($arDeals, $arDeal);
    return (count($arDeals) > 0) ? $arDeals : false;
}



function getCIBlockElementsByFilter_CRM_ENTITY($arFilter = array(),$sort = array()){
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



$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";



$urlArr=explode("/",$fullUrl);



$deal_ID=$urlArr[6];

$arFilter=array("IBLOCK_ID"=>17,
				"PROPERTY_DEAL"=>$deal_ID);
$gadaxdebiElement=getCIBlockElementsByFilter_CRM_ENTITY($arFilter);

$allGadaxda=array();

foreach($gadaxdebiElement as $singleGadaxda){
	$arPush=array();
	$arPush["DATE"]= $singleGadaxda["DATE"];
	$arPush["MONEY"]= $singleGadaxda["MONEY"];

	array_push($allGadaxda,$arPush);
}

$arFilter=array("ID"=>$deal_ID);
$deal=getDealsByFilter_CRM_ENTITY($arFilter);



$prods = CCrmDeal::LoadProductRows($deal_ID);

$prodID=$prods[0]["PRODUCT_ID"];

$arFilter = array(
    "ID" => $prodID,
);

$product = getCIBlockElementsByFilter_CRM_ENTITY($arFilter);




$gadaxdeb=$allGadaxda;

if($deal[0]["UF_CRM_1701270482886"] == 37){
	$priceGel=explode("|",$deal[0]["UF_CRM_1701416970265"]);
	$price=$priceGel[0];
	$valuta="GEL";
}elseif($deal[0]["UF_CRM_1701270482886"] == 38){
	$price=$deal[0]["OPPORTUNITY"];
	$valuta="USD";
}else{
	$valuta=$deal[0]["UF_CRM_1701270482886"];
	$price=$deal[0]["OPPORTUNITY"];

}

if(!$deal[0]["OPPORTUNITY"] || $deal[0]["OPPORTUNITY"] == 0){
	$price=0;
}
$priceGel=explode("|",$deal[0]["UF_CRM_1701416970265"]);

if($$priceGel[0]){
	$priceLari = $priceGel[0];
}else{
	$priceLari = 0;
}


$currentStage=$deal[0]["STAGE_ID"];
$recomendPrice=$product[0]["recomendedPrice"];



//// ================================== ჩემი ჩამატებული ===============================///////////




if($arResult['REST_USE'])
{
	$restSectionButtonID = "{$prefix}_rest_section";
	$arResult['REST_PLACEMENT_TAB_CONFIG']['bottom_button_id'] = $restSectionButtonID;
}

$htmlEditorConfigs = array();
$htmlFieldNames = isset($arResult['ENTITY_HTML_FIELD_NAMES']) && is_array($arResult['ENTITY_HTML_FIELD_NAMES'])
	? $arResult['ENTITY_HTML_FIELD_NAMES']
	: []
;
$bbFieldNames = isset($arResult['ENTITY_BB_FIELD_NAMES']) && is_array($arResult['ENTITY_BB_FIELD_NAMES'])
	? $arResult['ENTITY_BB_FIELD_NAMES']
	: []
;
foreach ($htmlFieldNames as $fieldName)
{
	$fieldPrefix = $prefix.'_'.strtolower($fieldName);
	$htmlEditorConfigs[$fieldName] = [
		'id' => "{$fieldPrefix}_html_editor",
		'containerId' => "{$fieldPrefix}_html_editor_container",
		'bb' => false,
		'controlsMap' => [
			['id' => 'Bold',  'compact' => true, 'sort' => 10],
			['id' => 'Italic',  'compact' => true, 'sort' => 20],
			['id' => 'Underline',  'compact' => true, 'sort' => 30],
			['id' => 'Strikeout',  'compact' => true, 'sort' => 40],
			['id' => 'RemoveFormat',  'compact' => false, 'sort' => 50],
			['id' => 'Color',  'compact' => false, 'sort' => 60],
			['id' => 'FontSelector',  'compact' => false, 'sort' => 70],
			['id' => 'FontSize',  'compact' => true, 'sort' => 80],
			['separator' => true, 'compact' => false, 'sort' => 90],
			['id' => 'OrderedList',  'compact' => true, 'sort' => 100],
			['id' => 'UnorderedList',  'compact' => true, 'sort' => 110],
			['id' => 'AlignList', 'compact' => false, 'sort' => 120],
			['separator' => true, 'compact' => false, 'sort' => 130],
			['id' => 'InsertLink',  'compact' => true, 'sort' => 140],
			['id' => 'Code',  'compact' => false, 'sort' => 180],
			['id' => 'Quote',  'compact' => false, 'sort' => 190],
			['separator' => true, 'compact' => false, 'sort' => 200],
			['id' => 'Fullscreen',  'compact' => true, 'sort' => 210],
			['id' => 'More',  'compact' => true, 'sort' => 400]
		],
	];
}
foreach ($bbFieldNames as $fieldName)
{
	$fieldPrefix = $prefix.'_'.strtolower($fieldName);
	$htmlEditorConfigs[$fieldName] = [
		'id' => "{$fieldPrefix}_html_editor",
		'containerId' => "{$fieldPrefix}_html_editor_container",
		'bb' => true,
		// only allow tags that are supported in mobile app
		'controlsMap' => [
			['id' => 'Bold',  'compact' => true, 'sort' => 10],
			['id' => 'Italic',  'compact' => true, 'sort' => 20],
			['id' => 'Underline',  'compact' => true, 'sort' => 30],
			['id' => 'Strikeout',  'compact' => true, 'sort' => 40],
			['id' => 'RemoveFormat',  'compact' => false, 'sort' => 50],
			['separator' => true, 'compact' => false, 'sort' => 90],
			['id' => 'OrderedList',  'compact' => true, 'sort' => 100],
			['id' => 'UnorderedList',  'compact' => true, 'sort' => 110],
			['separator' => true, 'compact' => false, 'sort' => 130],
			['id' => 'InsertLink',  'compact' => true, 'sort' => 140],
			['separator' => true, 'compact' => false, 'sort' => 200],
			['id' => 'Fullscreen',  'compact' => true, 'sort' => 210],
		],
	];
}

if(Main\Loader::includeModule('socialnetwork'))
{
	\CJSCore::init(array('socnetlogdest'));

	$destSort = CSocNetLogDestination::GetDestinationSort(
			array('DEST_CONTEXT' => \Bitrix\Crm\Entity\EntityEditor::getUserSelectorContext())
	);
	$last = array();
	CSocNetLogDestination::fillLastDestination($destSort, $last);

	$destUserIDs = array();
	if(isset($last['USERS']))
	{
		foreach($last['USERS'] as $code)
		{
			$destUserIDs[] = str_replace('U', '', $code);
		}
	}

	$dstUsers = CSocNetLogDestination::GetUsers(array('id' => $destUserIDs));
	$structure = CSocNetLogDestination::GetStucture(array('LAZY_LOAD' => true));
	$socnetGroups = CSocNetLogDestination::getSocnetGroup();

	$department = $structure['department'];
	$departmentRelation = $structure['department_relation'];
	$departmentRelationHead = $structure['department_relation_head'];
	?><script type="text/javascript">
		BX.ready(
			function()
			{
				BX.UI.EntityEditorUserSelector.users =  <?=CUtil::PhpToJSObject($dstUsers)?>;
				BX.UI.EntityEditorUserSelector.socnetGroups =  <?=CUtil::PhpToJSObject($socnetGroups)?>;
				BX.UI.EntityEditorUserSelector.department = <?=CUtil::PhpToJSObject($department)?>;
				BX.UI.EntityEditorUserSelector.departmentRelation = <?=CUtil::PhpToJSObject($departmentRelation)?>;
				BX.UI.EntityEditorUserSelector.last = <?=CUtil::PhpToJSObject(array_change_key_case($last, CASE_LOWER))?>;

				BX.Crm.EntityEditorCrmSelector.contacts = {};
				BX.Crm.EntityEditorCrmSelector.contactsLast = {};

				BX.Crm.EntityEditorCrmSelector.companies = {};
				BX.Crm.EntityEditorCrmSelector.companiesLast = {};

				BX.Crm.EntityEditorCrmSelector.leads = {};
				BX.Crm.EntityEditorCrmSelector.leadsLast = {};

				BX.Crm.EntityEditorCrmSelector.deals = {};
				BX.Crm.EntityEditorCrmSelector.dealsLast = {};
			}
		);
	</script><?
}

?><div class="crm-entity-card-container-content" id="<?=htmlspecialcharsbx($containerID)?>"></div>
<div class="crm-entity-card-widget-add-btn-container" id="<?=htmlspecialcharsbx($buttonContainerID)?>" style="display:none;">
	<span id="<?=htmlspecialcharsbx($createSectionButtonID)?>" class="crm-entity-add-widget-link">
		<?=GetMessage('CRM_ENTITY_ED_CREATE_SECTION')?>
	</span><?
if($arResult['REST_USE'])
{
	?><span id="<?=htmlspecialcharsbx($restSectionButtonID)?>" class="crm-entity-add-app-link">
		<?=GetMessage('CRM_ENTITY_ED_REST_SECTION_2')?>
	</span><?
}

if ($arResult['ENABLE_CONFIG_CONTROL'])
{
	$configMenuButtonID = "{$prefix}_config_menu";
	$configIconID = "{$prefix}_config_icon";

	$configIconClassName = $arResult['ENTITY_CONFIG_SCOPE'] === Crm\Entity\EntityEditorConfigScope::COMMON
		? 'crm-entity-card-common'
		: 'crm-entity-card-private';

	$configCaption = Crm\Entity\EntityEditorConfigScope::getCaption(
		$arResult['ENTITY_CONFIG_SCOPE'],
		$arResult['CONFIG_ID'],
		$arResult['USER_SCOPE_ID'],
		($arParams['MODULE_ID'] ?? null)
	);
	?><span id="<?=htmlspecialcharsbx($configIconID)?>" class="<?=$configIconClassName?>" title="<?=$configCaption?>">
	</span><?
	?><span id="<?=htmlspecialcharsbx($configMenuButtonID)?>" class="crm-entity-settings-link">
		<?=$configCaption?>
	</span><?php
}
?>
</div><?
if(!empty($htmlEditorConfigs))
{
	CModule::IncludeModule('fileman');
	foreach($htmlEditorConfigs as $htmlEditorConfig)
	{
		?><div id="<?=htmlspecialcharsbx($htmlEditorConfig['containerId'])?>" style="display:none;"><?
			$editor = new CHTMLEditor();
			$editor->Show(
				array(
					'name' => $htmlEditorConfig['id'],
					'id' => $htmlEditorConfig['id'],
					'siteId' => SITE_ID,
					'width' => '100%',
					'minBodyWidth' => 230,
					'normalBodyWidth' => 530,
					'height' => 200,
					'minBodyHeight' => 200,
					'showTaskbars' => false,
					'showNodeNavi' => false,
					'autoResize' => true,
					'autoResizeOffset' => 10,
					'bbCode' => $htmlEditorConfig['bb'],
					'saveOnBlur' => false,
					'bAllowPhp' => false,
					'lazyLoad' => true,
					'limitPhpAccess' => false,
					'setFocusAfterShow' => false,
					'askBeforeUnloadPage' => false,
					'useFileDialogs' => false,
					'controlsMap' => $htmlEditorConfig['controlsMap'],
				)
			);
		?></div><?
	}
}

?>

<?if (!empty($arResult['BIZPROC_MANAGER_CONFIG'])):
	$arResult['BIZPROC_MANAGER_CONFIG']['containerId'] = "{$prefix}_bizproc_manager_container";
?><div id="<?=htmlspecialcharsbx($arResult['BIZPROC_MANAGER_CONFIG']['containerId'])?>" style="display:none;"><?
	\CJSCore::init(array('bp_starter'));
	$APPLICATION->IncludeComponent("bitrix:bizproc.workflow.start",
		'modern',
		array(
			"MODULE_ID" => $arResult['BIZPROC_MANAGER_CONFIG']['moduleId'],
			"ENTITY" => $arResult['BIZPROC_MANAGER_CONFIG']['entity'],
			"DOCUMENT_TYPE" => $arResult['BIZPROC_MANAGER_CONFIG']['documentType'],
			"AUTO_EXECUTE_TYPE" => $arResult['BIZPROC_MANAGER_CONFIG']['autoExecuteType'],
		)
	);
?></div>
<?endif?>
<script type="text/javascript">
	BX.ready(
		function()
		{
			BX.message(<?=\Bitrix\Main\Web\Json::encode(Crm\Service\Container::getInstance()->getLocalization()->loadMessages())?>);

			BX.CrmEntityType.setCaptions(<?=CUtil::PhpToJSObject(CCrmOwnerType::GetJavascriptDescriptions())?>);
			BX.CrmEntityType.setNotFoundMessages(<?=CUtil::PhpToJSObject(CCrmOwnerType::GetNotFoundMessages())?>);
			<?php if (
				!empty($arResult['USERFIELD_TYPE_REST_CREATE_URL'])
				|| \Bitrix\Crm\Integration\Calendar::isResourceBookingAvailableForEntity($arResult['USER_FIELD_ENTITY_ID'])
			):?>
			BX.Event.EventEmitter.subscribe(
				'BX.UI.EntityUserFieldManager:getTypes',
				function(event)
				{
					var types = event.getData().types;
					if (!BX.Type.isArray(types))
					{
						return;
					}
					<?php if (\Bitrix\Crm\Integration\Calendar::isResourceBookingAvailableForEntity($arResult['USER_FIELD_ENTITY_ID'])):?>
						var index = 0;
						var length = types.length;
						for (; index < length; index++)
						{
							if (types[index].name === 'address')
							{
								break;
							}
						}
						types.splice(index, 0, {
							name: "resourcebooking",
							title: "<?=GetMessageJS('CRM_ENTITY_ED_UF_RESOURCEBOOKING_TITLE')?>",
							legend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_RESOURCEBOOKING_LEGEND')?>"
						});
					<?php endif;?>
					<?php if (!empty($arResult['USERFIELD_TYPE_REST_CREATE_URL'])):?>
						types.push({
							name: "rest_field_type",
							title: "<?=GetMessageJS('CRM_ENTITY_ED_UF_REST_TITLE')?>",
							legend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_REST_LEGEND')?>",
							callback: function()
							{
								BX.SidePanel.Instance.open(
									'<?=$arResult['USERFIELD_TYPE_REST_CREATE_URL']?>',
									{
										cacheable: false,
										allowChangeHistory: false
									}
								);
							}
						});
					<?php endif;?>
					event.setData({
						types: types
					});
				}
			);
			<?php endif;?>

			var contextParams = {
				CATEGORY_ID: <?=$arParams['EXTRAS']['CATEGORY_ID'] ?? 0?>
			};

			var userFieldManager = BX.UI.EntityUserFieldManager.create(
				"<?=CUtil::JSEscape($guid)?>",
				{
					entityId: <?=$arResult['ENTITY_ID']?>,
					enableCreation: <?=$arResult['ENABLE_USER_FIELD_CREATION'] ? 'true' : 'false'?>,
					fieldEntityId: "<?=CUtil::JSEscape($arResult['USER_FIELD_ENTITY_ID'])?>",
					creationSignature: "<?=CUtil::JSEscape($arResult['USER_FIELD_CREATE_SIGNATURE'])?>",
					creationPageUrl: "<?=CUtil::JSEscape($arResult['USER_FIELD_CREATE_PAGE_URL'])?>",
					languages: <?=CUtil::PhpToJSObject($arResult['LANGUAGES'])?>,
					fieldPrefix: "<?=CUtil::JSEscape($arResult['USER_FIELD_PREFIX'])?>",
					contextParams: contextParams,
				}
			);

			var config = BX.UI.EntityConfig.create(
				"<?=CUtil::JSEscape($arResult['CONFIG_ID'])?>",
				{
					data: <?=CUtil::PhpToJSObject($arResult['ENTITY_CONFIG'])?>,
					scope: "<?=CUtil::JSEscape($arResult['ENTITY_CONFIG_SCOPE'])?>",
					userScopes: <?=CUtil::PhpToJSObject($arResult['USER_SCOPES'])?>,
					userScopeId: "<?=CUtil::JSEscape($arResult['USER_SCOPE_ID'])?>",
					enableScopeToggle: <?=$arResult['ENABLE_CONFIG_SCOPE_TOGGLE'] ? 'true' : 'false'?>,
					canUpdatePersonalConfiguration: <?=$arResult['CAN_UPDATE_PERSONAL_CONFIGURATION'] ? 'true' : 'false'?>,
					canUpdateCommonConfiguration: <?=$arResult['CAN_UPDATE_COMMON_CONFIGURATION'] ? 'true' : 'false'?>,
					options: <?=CUtil::PhpToJSObject($arResult['ENTITY_CONFIG_OPTIONS'])?>,
                    categoryName: "<?=CUtil::JSEscape($arResult['ENTITY_CONFIG_CATEGORY_NAME'])?>"
				}
			);

			var scheme = BX.UI.EntityScheme.create(
				"<?=CUtil::JSEscape($guid)?>",
				{
					current: <?=CUtil::PhpToJSObject($arResult['ENTITY_SCHEME'])?>,
					available: <?=CUtil::PhpToJSObject($arResult['ENTITY_AVAILABLE_FIELDS'])?>
				}
			);

			BX.UI.EntitySchemeElement.userFieldFileUrlTemplate = "<?=CUtil::JSEscape($arResult['USER_FIELD_FILE_URL_TEMPLATE'])?>";

			var model = BX.Crm.EntityEditorModelFactory.create(
				<?=$arResult['ENTITY_TYPE_ID']?>,
				"",
				{ entityTypeId: <?=$arResult['ENTITY_TYPE_ID']?>, data: <?=CUtil::PhpToJSObject($arResult['ENTITY_DATA'])?> }
			);

			BX.CrmDuplicateSummaryPopup.messages =
			{
				title: "<?=GetMessageJS("CRM_ENTITY_ED_DUP_CTRL_SHORT_SUMMARY_TITLE")?>"
			};

			BX.CrmDuplicateWarningDialog.messages =
			{
				title: "<?=GetMessageJS("CRM_ENTITY_ED_DUP_CTRL_WARNING_DLG_TITLE")?>",
				acceptButtonTitle: "<?=GetMessageJS("CRM_ENTITY_ED_DUP_CTRL_WARNING_ACCEPT_BTN_TITLE")?>",
				cancelButtonTitle: "<?=GetMessageJS("CRM_ENTITY_ED_DUP_CTRL_WARNING_CANCEL_BTN_TITLE")?>"
			};

			BX.CrmEntityType.categoryCaptions = <?=CUtil::PhpToJSObject(\CCrmOwnerType::GetAllCategoryCaptions(true))?>;

			BX.Crm.EntityEditor.messages =
			{
				newSectionTitle: "<?=GetMessageJS('CRM_ENTITY_ED_NEW_SECTION_TITLE')?>",
				inlineEditHint: "<?=GetMessageJS('CRM_ENTITY_ED_INLINE_EDIT_HINT')?>",
				resetConfig: "<?=GetMessageJS('CRM_ENTITY_ED_RESET_CONFIG_2')?>",
				forceCommonConfigForAllUsers: "<?=GetMessageJS('CRM_ENTITY_ED_FORCE_COMMON_CONFIG_FOR_ALL')?>",
				switchToPersonalConfig: "<?=GetMessageJS('CRM_ENTITY_ED_SWITCH_TO_PERSONAL_CONFIG')?>",
				switchToCommonConfig: "<?=GetMessageJS('CRM_ENTITY_ED_SWITCH_TO_COMMON_CONFIG')?>",
				couldNotFindEntityIdError: "<?=GetMessageJS('CRM_ENTITY_ED_COULD_NOT_FIND_ENTITY_ID')?>",
				titleEdit: "<?=GetMessageJS('CRM_ENTITY_ED_TITLE_EDIT')?>",
				titleEditUnsavedChanges: "<?=GetMessageJS('CRM_ENTITY_ED_TITLE_EDIT_UNSAVED_CHANGES')?>",
				checkScope: "<?=GetMessageJS('CRM_ENTITY_ED_CHECK_SCOPE')?>",
				createScope: "<?=GetMessageJS('CRM_ENTITY_ED_CREATE_SCOPE')?>",
				updateScope: "<?=GetMessageJS('CRM_ENTITY_ED_UPDATE_SCOPE')?>",
			};

			BX.Crm.EntityEditorScopeConfig.messages =
			{
				createScope: "<?=GetMessageJS('CRM_ENTITY_ED_CREATE_SCOPE')?>",
				scopeName: "<?=GetMessageJS('CRM_ENTITY_ED_CONFIG_SCOPE_NAME')?>",
				scopeNamePlaceholder: "<?=GetMessageJS('CRM_ENTITY_ED_CONFIG_SCOPE_NAME_PLACEHOLDER')?>",
				scopeMembers: "<?=GetMessageJS('CRM_ENTITY_ED_CONFIG_SCOPE_MEMBERS')?>",
				scopeSave: "<?=GetMessageJS('CRM_ENTITY_ED_CONFIG_SCOPE_SAVE')?>",
				scopeCancel: "<?=GetMessageJS('CRM_ENTITY_ED_CONFIG_SCOPE_CANCEL')?>",
				scopeSaved: "<?=GetMessageJS('CRM_ENTITY_ED_CONFIG_SCOPE_SAVED')?>",
			};

			BX.UI.EntityUserFieldManager.messages =
			{
				stringLabel: "<?=GetMessageJS('CRM_ENTITY_ED_UF_STRING_LABEL')?>",
				doubleLabel: "<?=GetMessageJS('CRM_ENTITY_ED_UF_DOUBLE_LABEL')?>",
				moneyLabel: "<?=GetMessageJS('CRM_ENTITY_ED_UF_MONEY_LABEL')?>",
				datetimeLabel: "<?=GetMessageJS('CRM_ENTITY_ED_UF_DATETIME_LABEL')?>",
				enumerationLabel: "<?=GetMessageJS('CRM_ENTITY_ED_UF_ENUMERATION_LABEL')?>",
				fileLabel: "<?=GetMessageJS('CRM_ENTITY_ED_UF_FILE_LABEL')?>",
				label: "<?=GetMessageJS('CRM_ENTITY_ED_UF_LABEL')?>",
				stringTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_STRING_TITLE')?>",
				stringLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_STRING_LEGEND')?>",
				doubleTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_DOUBLE_TITLE')?>",
				doubleLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_DOUBLE_LEGEND')?>",
				moneyTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_MONEY_TITLE')?>",
				moneyLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_MONEY_LEGEND')?>",
				booleanTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_BOOLEAN_TITLE')?>",
				booleanLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_BOOLEAN_LEGEND')?>",
				datetimeTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_DATETIME_TITLE')?>",
				datetimeLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_DATETIME_LEGEND')?>",
				enumerationTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_ENUM_TITLE')?>",
				enumerationLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_ENUM_LEGEND')?>",
				urlTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_URL_TITLE')?>",
				urlLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_URL_LEGEND')?>",
				addressTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_ADDRESS_TITLE_2')?>",
				addressLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_ADDRESS_LEGEND_2')?>",
				resourcebookingTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_RESOURCEBOOKING_TITLE')?>",
				resourcebookingLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_RESOURCEBOOKING_LEGEND')?>",
				fileTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_FILE_TITLE')?>",
				fileLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_FILE_LEGEND')?>",
				customTitle: "<?=GetMessageJS('CRM_ENTITY_ED_UF_CUSTOM_TITLE')?>",
				customLegend: "<?=GetMessageJS('CRM_ENTITY_ED_UF_CUSTOM_LEGEND')?>"
			};

			BX.UI.EntityUserFieldManager.additionalTypeList = <?=\CUtil::PhpToJSObject($arResult['USERFIELD_TYPE_ADDITIONAL'])?>;

			BX.Crm.EntityEditorMoneyPay.messages =
			{
				popupItemTitle: "<?=GetMessageJS('CRM_ENTITY_EM_BUTTON_PAY_POPUP_ITEM_TITLE')?>",
				payButtonLabel: "<?=GetMessageJS('CRM_ENTITY_EM_BUTTON_PAY')?>",
				showPayButton: "<?=GetMessageJS('CRM_ENTITY_EM_SHOW_BUTTON_PAY')?>",
				hidePayButton: "<?=GetMessageJS('CRM_ENTITY_EM_HIDE_BUTTON_PAY')?>",
			};

			BX.UI.EntityEditorFieldConfigurator.messages =
			{
				labelField: "<?=GetMessageJS('CRM_ENTITY_ED_FIELD_TITLE')?>",
				showAlways: "<?=GetMessageJS('CRM_ENTITY_ED_SHOW_ALWAYS')?>",
				useTimezone: "<?=GetMessageJS('CRM_ENTITY_ED_USE_TIMEZONE')?>",
			};

			BX.Crm.EntityFieldVisibilityConfigurator.messages =
			{
				titleField: "<?=GetMessageJS('CRM_VISIBILITY_ATTR_TITLE')?>",
				labelField: "<?=GetMessageJS('CRM_VISIBILITY_ATTR_LABEL')?>",
				addUserButton: "<?=GetMessageJS('CRM_VISIBILITY_ADD_USER_BUTTON')?>",
			};

			BX.Crm.EntityEditorUserFieldConfigurator.messages =
			{
				labelField: "<?=GetMessageJS('CRM_ENTITY_ED_FIELD_TITLE')?>",
				isRequiredField: "<?=GetMessageJS('CRM_ENTITY_ED_UF_REQUIRED_FIELD')?>",
				isMultipleField: "<?=GetMessageJS('CRM_ENTITY_ED_UF_MULTIPLE_FIELD')?>",
				showAlways: "<?=GetMessageJS('CRM_ENTITY_ED_SHOW_ALWAYS')?>",
				enableTime: "<?=GetMessageJS('CRM_ENTITY_ED_UF_ENABLE_TIME')?>",
				enumItems: "<?=GetMessageJS('CRM_ENTITY_ED_UF_ENUM_ITEMS')?>",
				add: "<?=GetMessageJS('CRM_ENTITY_ED_ADD')?>"
			};

			BX.UI.EntityEditorField.messages =
			{
				hideButtonHint: "<?=GetMessageJS('CRM_ENTITY_ED_HIDE_BUTTON_HINT')?>",
				hideButtonDisabledHint: "<?=GetMessageJS('CRM_ENTITY_ED_HIDE_BUTTON_DISABLED_HINT')?>",
				requiredFieldError: "<?=GetMessageJS('CRM_ENTITY_ED_REQUIRED_FIELD_ERROR')?>",
				add: "<?=GetMessageJS('CRM_ENTITY_ED_ADD')?>",
				hide: "<?=GetMessageJS('CRM_ENTITY_ED_HIDE')?>",
				showAlways: "<?=GetMessageJS('CRM_ENTITY_ED_SHOW_ALWAYS')?>",
				configure: "<?=GetMessageJS('CRM_ENTITY_ED_CONFIGURE')?>",
				isEmpty: "<?=GetMessageJS('CRM_ENTITY_ED_FIELD_EMPTY')?>",
				hideDeniedDlgTitle: "<?=GetMessageJS('CRM_ENTITY_ED_HIDE_TITLE')?>",
				hideDeniedDlgContent: "<?=GetMessageJS('CRM_ENTITY_ED_HIDE_DENIED')?>",
				hiddenInViewMode: "<?=GetMessageJS('CRM_ENTITY_ED_FIELD_HIDDEN_IN_VIEW_MODE')?>",
				isHiddenDueToShowAlwaysChanged: "<?=GetMessageJS('CRM_ENTITY_ED_FIELD_HIDDEN_DUE_TO_SHOW_ALWAYS_CHANGED')?>"
			};

			BX.Crm.EntityEditorUserField.messages =
			{
				moveAddrToRequisite: "<?=GetMessageJS('CRM_ENTITY_ED_MOVE_ADDR_TO_REQUISITE')?>",
				moveAddrToRequisiteHtml: "<?=GetMessageJS('CRM_ENTITY_ED_MOVE_ADDR_TO_REQUISITE_HTML')?>",
				moveAddrToRequisiteBtnStart: "<?=GetMessageJS('CRM_ENTITY_ED_MOVE_ADDR_TO_REQUISITE_BTN_START')?>",
				moveAddrToRequisiteBtnCancel: "<?=GetMessageJS('CRM_ENTITY_ED_MOVE_ADDR_TO_REQUISITE_BTN_CANCEL')?>",
				moveAddrToRequisiteStartSuccess: "<?=GetMessageJS('CRM_ENTITY_ED_MOVE_ADDR_TO_REQUISITE_START_SUCCESS')?>"
			};

			BX.Crm.EntityEditorSection.messages =
			{
				change: "<?=GetMessageJS('CRM_ENTITY_ED_CHANGE')?>",
				cancel: "<?=GetMessageJS('CRM_ENTITY_ED_CANCEL')?>",
				createField: "<?=GetMessageJS('CRM_ENTITY_ED_CREATE_FIELD')?>",
				selectField: "<?=GetMessageJS('CRM_ENTITY_ED_SELECT_FIELD')?>",
				deleteSection: "<?=GetMessageJS('CRM_ENTITY_ED_DELETE_SECTION')?>",
				deleteSectionConfirm: "<?=GetMessageJS('CRM_ENTITY_ED_DELETE_SECTION_CONFIRM')?>",
				selectFieldFromOtherSection: "<?=GetMessageJS('CRM_ENTITY_ED_SELECT_FIELD_FROM_OTHER_SECTION')?>",
				transferDialogTitle: "<?=GetMessageJS('CRM_ENTITY_ED_FIELD_TRANSFER_DIALOG_TITLE')?>",
				nothingSelected: "<?=GetMessageJS('CRM_ENTITY_ED_NOTHIG_SELECTED')?>",
				deleteSectionDenied: "<?=GetMessageJS('CRM_ENTITY_ED_DELETE_SECTION_DENIED')?>",
				openDetails: "<?=GetMessageJS('CRM_ENTITY_ED_SECTION_OPEN_DETAILS')?>"
			};

			BX.UI.EntityEditorBoolean.messages =
			{
				yes: "<?=GetMessageJS('MAIN_YES')?>",
				no: "<?=GetMessageJS('MAIN_NO')?>"
			};

			BX.Crm.EntityEditorUser.messages =
			{
				change: "<?=GetMessageJS('CRM_ENTITY_ED_CHANGE_USER')?>"
			};

			BX.Crm.EntityEditorMultipleUser.messages =
				{
					change: "<?=GetMessageJS('CRM_ENTITY_ED_CHANGE_USER')?>"
				};

			BX.Crm.EntityEditorFileStorage.messages =
			{
				diskAttachFiles: "<?=GetMessageJS('CRM_ENTITY_ED_DISK_ATTACH_FILE')?>",
				diskAttachedFiles: "<?=GetMessageJS('CRM_ENTITY_ED_DISK_ATTACHED_FILES')?>",
				diskSelectFile: "<?=GetMessageJS('CRM_ENTITY_ED_DISK_SELECT_FILE')?>",
				diskSelectFileLegend: "<?=GetMessageJS('CRM_ENTITY_ED_DISK_SELECT_FILE_LEGEND')?>",
				diskUploadFile: "<?=GetMessageJS('CRM_ENTITY_ED_DISK_UPLOAD_FILE')?>",
				diskUploadFileLegend: "<?=GetMessageJS('CRM_ENTITY_ED_DISK_UPLOAD_FILE_LEGEND')?>"
			};

			BX.UI.EntityEditorHtml.messages =
			{
				expand: "<?=GetMessageJS('CRM_ENTITY_ED_EXPAND_COMMENT')?>",
				collapse: "<?=GetMessageJS('CRM_ENTITY_ED_COLLAPSE_COMMENT')?>"
			};

			BX.Crm.PrimaryClientEditor.messages =
			{
				select: "<?=GetMessageJS('CRM_ENTITY_ED_SELECT')?>",
				bind: "<?=GetMessageJS('CRM_ENTITY_ED_BIND')?>",
				create: "<?=GetMessageJS('CRM_ENTITY_ED_CREATE')?>"
			};

			BX.Crm.SecondaryClientEditor.messages =
			{
				select: "<?=GetMessageJS('CRM_ENTITY_ED_SELECT')?>",
				create: "<?=GetMessageJS('CRM_ENTITY_ED_CREATE')?>",
				bind: "<?=GetMessageJS('CRM_ENTITY_ED_BIND')?>",
				addParticipant: "<?=GetMessageJS('CRM_ENTITY_ED_ADD_PARTICIPANT')?>"
			};

			BX.Crm.EntityEditorClientSearchBox.messages =
			{
				contactToCreateTag: "<?=GetMessageJS('CRM_ENTITY_ED_NEW_CONTACT')?>",
				companyToCreateTag: "<?=GetMessageJS('CRM_ENTITY_ED_NEW_COMPANY')?>",
				contactToCreateLegend: "<?=GetMessageJS('CRM_ENTITY_ED_NEW_CONTACT_LEGEND')?>",
				companyToCreateLegend: "<?=GetMessageJS('CRM_ENTITY_ED_NEW_COMPANY_LEGEND')?>",
				contactChangeButtonHint: "<?=GetMessageJS('CRM_ENTITY_ED_CONTACT_CHANGE_BUTTON_HINT')?>",
				companyChangeButtonHint: "<?=GetMessageJS('CRM_ENTITY_ED_COMPANY_CHANGE_BUTTON_HINT')?>",
				entityEditTag: "<?=GetMessageJS('CRM_ENTITY_ED_EDIT_TAG')?>",
				notFound: "<?=GetMessageJS('CRM_ENTITY_ED_NOT_FOUND')?>",
				unnamed: "<?=CUtil::JSEscape(\CCrmContact::GetDefaultName())?>",
				untitled: "<?=CUtil::JSEscape(\CCrmCompany::GetDefaultTitle())?>",
				companyChangeButtonHint: "<?=GetMessageJS('CRM_ENTITY_ED_COMPANY_CHANGE_BUTTON_HINT')?>",
				notifyContactToDeal: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_CONTACT_TO_DEAL')?>",
				notifyCompanyToDeal: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_COMPANY_TO_DEAL')?>",
				notifyContactToLead: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_CONTACT_TO_LEAD')?>",
				notifyCompanyToLead: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_COMPANY_TO_LEAD')?>",
				notifyContactToSmartInvoice: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_CONTACT_TO_INVOICE')?>",
				notifyCompanyToSmartInvoice: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_COMPANY_TO_INVOICE')?>",
				notifyContactToCompany: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_CONTACT_TO_COMPANY')?>",
				notifyCompanyToContact: "<?=GetMessageJS('CRM_CLIENT_EDITOR_NOTIFY_COMPANY_TO_CONTACT')?>",
			};

			BX.Crm.ClientEditorCommunicationButton.messages =
			{
				telephonyNotSupported: "<?=GetMessageJS('CRM_ENTITY_ED_TELEPHONY_NOT_SUPPORTED')?>",
				messagingNotSupported: "<?=GetMessageJS('CRM_ENTITY_ED_MESSAGING_NOT_SUPPORTED')?>"
			};

			BX.Crm.EntityEditorEntity.messages =
			{
				select: "<?=GetMessageJS('CRM_ENTITY_ED_SELECT')?>"
			};

			BX.Crm.EntityEditorClientLight.messages =
			{
				addParticipant: "<?=GetMessageJS('CRM_ENTITY_ED_ADD_PARTICIPANT')?>",
				companySearchPlaceholder: "<?=GetMessageJS('CRM_ENTITY_ED_COMPANY_SEARCH_PLACEHOLDER_2')?>",
				contactSearchPlaceholder: "<?=GetMessageJS('CRM_ENTITY_ED_CONTACT_SEARCH_PLACEHOLDER_2')?>",
				enableCompany: "<?=GetMessageJS('CRM_ENTITY_ED_ENABLE_CLIENT_COMPANY')?>",
				disableCompany: "<?=GetMessageJS('CRM_ENTITY_ED_DISABLE_CLIENT_COMPANY')?>",
				enableContact: "<?=GetMessageJS('CRM_ENTITY_ED_ENABLE_CLIENT_CONTACT')?>",
				disableContact: "<?=GetMessageJS('CRM_ENTITY_ED_DISABLE_CLIENT_CONTACT')?>",
				enableAddress: "<?=GetMessageJS('CRM_ENTITY_ED_ENABLE_CLIENT_ADDRESS')?>",
				disableAddress: "<?=GetMessageJS('CRM_ENTITY_ED_DISABLE_CLIENT_ADDRESS')?>",
				enableRequisites: "<?=GetMessageJS('CRM_ENTITY_ED_ENABLE_CLIENT_REQUISITES')?>",
				disableRequisites: "<?=GetMessageJS('CRM_ENTITY_ED_DISABLE_CLIENT_REQUISITES')?>",
				displayContactAtFirst: "<?=GetMessageJS('CRM_ENTITY_ED_DISPLAY_CONTACT_AT_FIRST')?>",
				displayCompanyAtFirst: "<?=GetMessageJS('CRM_ENTITY_ED_DISPLAY_COMPANY_AT_FIRST')?>",
				enableQuickEdit: "<?=GetMessageJS('CRM_ENTITY_ED_ENABLE_QUICK_EDIT')?>",
				disableQuickEdit: "<?=GetMessageJS('CRM_ENTITY_ED_DISABLE_QUICK_EDIT')?>"
			};

			BX.Crm.EntityEditorMoney.messages =
			{
				manualOpportunitySetAutomatic: "<?=GetMessageJS('CRM_EDITOR_MANUAL_OPPORTUNITY_SET_TO_AUTOMATIC')?>"
			}

			BX.Crm.EntityEditorProductRowProxy.messages =
			{
				manualOpportunityConfirmationTitle: "<?=GetMessageJS('CRM_EDITOR_MANUAL_OPPORTUNITY_CONFIRMATION_TITLE')?>",
				manualOpportunityConfirmationText: "<?=GetMessageJS('CRM_EDITOR_MANUAL_OPPORTUNITY_CONFIRMATION_TEXT')?>",
				manualOpportunityConfirmationYes: "<?=GetMessageJS('MAIN_YES')?>",
				manualOpportunityConfirmationNo: "<?=GetMessageJS('MAIN_NO')?>",
				manualOpportunityChangeModeTitle: "<?=
					empty($arResult['MESSAGES']['MANUAL_OPPORTUNITY_CHANGE_MODE_TITLE'])
						? GetMessageJS('CRM_EDITOR_MANUAL_OPPORTUNITY_CHANGE_TITLE_' . $arResult['ENTITY_TYPE_ID'])
						: \CUtil::JSEscape($arResult['MESSAGES']['MANUAL_OPPORTUNITY_CHANGE_MODE_TITLE'])
					?>",
				manualOpportunityChangeModeText: "<?=
					empty($arResult['MESSAGES']['MANUAL_OPPORTUNITY_CHANGE_MODE_TEXT'])
						? GetMessageJS('CRM_EDITOR_MANUAL_OPPORTUNITY_CHANGE_TEXT_' . $arResult['ENTITY_TYPE_ID'])
						: \CUtil::JSEscape($arResult['MESSAGES']['MANUAL_OPPORTUNITY_CHANGE_MODE_TEXT'])
					?>",
				manualOpportunityChangeModeYes: "<?=GetMessageJS('CRM_EDITOR_MANUAL_OPPORTUNITY_CHANGE_VALUE_AUTO')?>",
				manualOpportunityChangeModeNo: "<?=GetMessageJS('CRM_EDITOR_MANUAL_OPPORTUNITY_CHANGE_VALUE_MANUAL')?>"
			};

			BX.Crm.EntityProductListController.messages = BX.Crm.EntityEditorProductRowProxy.messages;

			BX.Crm.ClientEditorEntityRequisitePanel.messages =
			{
				toggle: "<?=GetMessageJS('CRM_ENTITY_ED_TOGGLE_REQUISITES')?>"
			};

			BX.Crm.RequisiteNavigator.messages =
			{
				next: "<?=GetMessageJS('CRM_ENTITY_ED_NAVIGATION_NEXT')?>",
				toggle: "<?=GetMessageJS('CRM_ENTITY_ED_TOGGLE_REQUISITES')?>",
				legend: "<?=GetMessageJS('CRM_ENTITY_ED_NAVIGATION_LEGEND')?>",
				stub: "<?=GetMessageJS('CRM_ENTITY_ED_NO_REQUISITE_STUB')?>"
			};

			BX.Crm.EntityEditorRequisiteSelector.messages =
			{
				bankDetails: "<?=GetMessageJS('CRM_ENTITY_ED_BANK_DETAILS')?>"
			};

			BX.Crm.EntityEditorRequisiteListItem.messages =
			{
				deleteTitle: "<?=GetMessageJS("CRM_ENTITY_ED_REQUISITE_DELETE_DLG_TITLE")?>",
				deleteConfirm: "<?=GetMessageJS("CRM_ENTITY_ED_REQUISITE_DELETE_DLG_CONTENT")?>"
			};

			BX.Crm.EntityEditorRequisiteList.messages =
			{
				deleteTitle: "<?=GetMessageJS("CRM_ENTITY_ED_REQUISITE_DELETE_DLG_TITLE")?>",
				deleteConfirm: "<?=GetMessageJS("CRM_ENTITY_ED_REQUISITE_DELETE_DLG_CONTENT")?>"
			};

			BX.Crm.EntityEditorRecurring.messages =
			{
				notRepeat: "<?=GetMessageJS('CRM_ENTITY_ED_RECURRING_NOT_REPEAT')?>",
				modeTitle: "<?=GetMessageJS('CRM_ENTITY_ED_RECURRING_MODE_TITLE')?>",
				hide: "<?=GetMessageJS('CRM_ENTITY_ED_HIDE')?>"
			};

			BX.Crm.EntityEditorRecurringSingleField.messages =
			{
				until: "<?=GetMessageJS('CRM_ENTITY_ED_RECURRING_UNTIL')?>"
			};

			BX.Crm.EntityEditorPayment.messages =
			{
				paymentWasPaid: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_WAS_PAID')?>",
				paymentWasNotPaid: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_WAS_NOT_PAID')?>",
				paymentCancel: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_CANCEL')?>",
				paymentReturn: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_RETURN')?>",
				documentTitle: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_DOCUMENT_TITLE')?>",
				addDocument: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_ADD_DOCUMENT')?>",
				sum: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_SUM')?>",
			};

			BX.Crm.EntityEditorOrderController.messages =
			{
				saveChanges: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_SAVE_CHANGES')?>",
				saveConfirm: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_SAVE_CONFIRM')?>",
				save: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_SAVE')?>",
				notSave: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_NOT_SAVE')?>"
			};

			BX.Crm.EntityEditorPaymentStatus.messages =
			{
				paymentWasPaid: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_WAS_PAID')?>",
				paymentWasNotPaid: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_WAS_NOT_PAID')?>",
				paymentCancel: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_CANCEL')?>",
				paymentReturn: "<?=GetMessageJS('CRM_ENTITY_ED_PAYMENT_RETURN')?>"
			};

			BX.Crm.EntityEditorShipment.messages =
			{
				deliveryAllowed: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_ALLOWED')?>",
				deducted: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DEDEUCTED')?>",
				trackingNumberTitle: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_TRACKING_NUMBER_TITLE')?>",
				documentTitle: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DOCUMENT_TITLE')?>",
				addDocument: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_ADD_DOCUMENT')?>",
				deliveryService: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_SERVICE')?>",
				deliveryProfile: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_PROFILE')?>",
				price: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_PRICE')?>",
				profile: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_PROFILE')?>",
				deliveryPriceCalculated: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_PRICE_CALCULATED')?>",
				deliveryPriceCalculatedHint: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_PRICE_CALCULATED_HINT')?>",
				deliveryStore: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_STORE')?>",
				refresh: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_REFRESH')?>",
			};

			BX.Crm.EntityEditorDeliverySelector.messages =
			{
				notSelected: "<?=GetMessageJS('CRM_ENTITY_ED_DELIVERY_SELECTOR_NOT_SELECTED')?>",
				deliveryStore: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_STORE')?>",
				deliveryProfile: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_DELIVERY_PROFILE')?>",
			};

			BX.Crm.EntityEditorPaySystemSelector.messages =
			{
				notSelected: "<?=GetMessageJS('CRM_ENTITY_ED_PAY_SYSTEM_SELECTOR_NOT_SELECTED')?>",
			};

			BX.Crm.EntityEditorOrderPropertySubsection.messages =
			{
				linkToSettings: "<?=GetMessageJS('CRM_ENTITY_ED_CHILD_ENTITY_MENU_SETTINGS_LINK')?>"
			};
			BX.Crm.EntityEditorOrderPropertyWrapper.messages =
			{
				createField: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PROPERTY_CREATE')?>",
				insertField: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PROPERTY_INSERT')?>",
				selectField: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PROPERTY_SELECT')?>",
				disabledBlockTitle: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PROPERTY_UNKNOWN_GROUP')?>"
			};

			BX.Crm.EntityEditorPaymentCheck.messages =
			{
				titleFieldSum: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PAYMENT_CHECK_SUM')?>",
				titleFieldDateCreate: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PAYMENT_CHECK_DATE_CREATE')?>",
				titleFieldType: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PAYMENT_CHECK_TYPE')?>",
				titleFieldCashBoxName: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PAYMENT_CHECK_CASHBOX_NAME')?>",
				titleFieldStatus: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PAYMENT_CHECK_STATUS')?>",
				titleFieldLink: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PAYMENT_CHECK_LINK')?>",
				emptyCheckList: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PAYMENT_CHECK_EMPTY')?>"
			};

			BX.Crm.EntityEditorOrderProductProperty.messages =
			{
				addProductProperty: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_ADD_PRODUCT_PROPERTY_LINK')?>",
				fieldBlockTitle: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PRODUCT_FIELD_BLOCK_TITLE')?>",
				fieldTitleName: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PRODUCT_FIELD_NAME')?>",
				fieldTitleValue: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PRODUCT_FIELD_VALUE')?>",
				fieldTitleCode: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PRODUCT_FIELD_CODE')?>",
				fieldTitleSort: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_PRODUCT_FIELD_SORT')?>"
			};
			BX.Crm.EntityEditorOrderUser.messages =
			{
				change: "<?=GetMessageJS('CRM_ENTITY_ED_CHANGE_USER')?>",
				searchPlaceholder: "<?=GetMessageJS('CRM_ENTITY_ED_ORDER_USER_SEARCH_PLACEHOLDER')?>",
			};

			BX.Crm.EntityEditorOrderClientSearchBox.messages =
			{
				notFound: "<?=GetMessageJS('CRM_ENTITY_ED_NOT_FOUND')?>"
			};

			BX.Crm.EntityEditorCalculatedDeliveryPrice.messages =
			{
				refresh: "<?=GetMessageJS('CRM_ENTITY_ED_SHIPMENT_REFRESH')?>",
			};

			BX.Crm.EntityEditorDocumentNumber.messages =
			{
				numeratorSettingsContextItem: "<?=GetMessageJS('CRM_ENTITY_ED_DOCUMENT_NUMBER_NUMERATOR_SETTINGS_CONTEXT_ITEM')?>",
			};

			BX.message(
				{
					"CRM_EDITOR_SAVE": "<?=GetMessageJS('CRM_ENTITY_ED_SAVE')?>",
					"CRM_EDITOR_CONTINUE": "<?=GetMessageJS('CRM_ENTITY_ED_CONTINUE')?>",
					"CRM_EDITOR_CANCEL": "<?=GetMessageJS('CRM_ENTITY_ED_CANCEL')?>",
					"CRM_EDITOR_DELETE": "<?=GetMessageJS('CRM_ENTITY_ED_DELETE')?>",
					"CRM_EDITOR_ADD": "<?=GetMessageJS('CRM_ENTITY_ED_ADD')?>",
					"CRM_EDITOR_ADD_REQUISITE": "<?=GetMessageJS('CRM_EDITOR_ADD_REQUISITE')?>",
					"CRM_EDITOR_ADD_BANK_DETAILS": "<?=GetMessageJS('CRM_EDITOR_ADD_BANK_DETAILS')?>",
					"CRM_EDITOR_CONFIRMATION": "<?=GetMessageJS('CRM_EDITOR_CONFIRMATION')?>",
					"CRM_EDITOR_CLOSE_CONFIRMATION": "<?=GetMessageJS('CRM_EDITOR_CLOSE_CONFIRMATION')?>",
					"CRM_EDITOR_SAVE_ERROR_TITLE": "<?=GetMessageJS('CRM_EDITOR_SAVE_ERROR_TITLE')?>",
					"CRM_EDITOR_SAVE_ERROR_CONTENT": "<?=GetMessageJS('CRM_EDITOR_SAVE_ERROR_CONTENT')?>",
					"CRM_EDITOR_PAYMENT_PAID": "<?=GetMessageJS('CRM_EDITOR_PAYMENT_PAID')?>",
					"CRM_EDITOR_PAYMENT_NOT_PAID": "<?=GetMessageJS('CRM_EDITOR_PAYMENT_NOT_PAID')?>",
					"CRM_EDITOR_CANCEL_CONFIRMATION": "<?=GetMessageJS('CRM_EDITOR_CANCEL_CONFIRMATION')?>",
					"CRM_EDITOR_YES": "<?=GetMessageJS('MAIN_YES')?>",
					"CRM_EDITOR_NO": "<?=GetMessageJS('MAIN_NO')?>",
					"CRM_EDITOR_PHONE": "<?=GetMessageJS('CRM_EDITOR_PHONE')?>",
					"CRM_EDITOR_EMAIL": "<?=GetMessageJS('CRM_EDITOR_EMAIL')?>",
					"CRM_EDITOR_ADDRESS": "<?=GetMessageJS('CRM_EDITOR_ADDRESS')?>",
					"CRM_EDITOR_REQUISITES": "<?=GetMessageJS('CRM_EDITOR_REQUISITES')?>",
					"CRM_EDITOR_PLACEMENT_CAUTION": "<?=GetMessageJS('CRM_EDITOR_PLACEMENT_CAUTION')?>",
				}
			);

			BX.Crm.EntityPhaseLayout.colors =
				{
					process: "<?=Bitrix\Crm\Color\PhaseColorScheme::PROCESS_COLOR?>",
					success: "<?=Bitrix\Crm\Color\PhaseColorScheme::SUCCESS_COLOR?>",
					failure: "<?=Bitrix\Crm\Color\PhaseColorScheme::FAILURE_COLOR?>",
					apology: "<?=Bitrix\Crm\Color\PhaseColorScheme::FAILURE_COLOR?>"
				};

			var bizprocManager = null;
			var restPlacementTabManager = null;
			<?if(!$arResult['IS_EMBEDDED']){?>
			bizprocManager = BX.Crm.EntityBizprocManager.create(
				"<?=CUtil::JSEscape($guid)?>",
				<?=\Bitrix\Main\Web\Json::encode($arResult['BIZPROC_MANAGER_CONFIG'])?>
			);

			restPlacementTabManager = BX.Crm.EntityRestPlacementManager.create(
				"<?=CUtil::JSEscape($guid)?>",
				<?=\CUtil::PhpToJSObject($arResult['REST_PLACEMENT_TAB_CONFIG'])?>
			);
			<?}?>

			BX.Crm.EntityEditor.setDefault(
				BX.Crm.EntityEditor.create(
					"<?=CUtil::JSEscape($guid)?>",
					{
						entityTypeName: "<?=CUtil::JSEscape($arResult['ENTITY_TYPE_NAME'])?>",
						entityTypeId: <?=$arResult['ENTITY_TYPE_ID']?>,
						entityTypeTitle: "<?= CUtil::JSEscape($arResult['ENTITY_TYPE_TITLE']) ?>",
						useFieldsSearch: true,
						useForceFieldsAdd: true,
						entityId: <?=$arResult['ENTITY_ID']?>,
						model: model,
						config: config,
						moduleId: 'crm',
						scheme: scheme,
						validators: <?=CUtil::PhpToJSObject($arResult['ENTITY_VALIDATORS'])?>,
						controllers: <?=CUtil::PhpToJSObject($arResult['ENTITY_CONTROLLERS'])?>,
						detailManagerId: "<?=CUtil::JSEscape($arResult['DETAIL_MANAGER_ID'])?>",
						userFieldManager: userFieldManager,
						bizprocManager: bizprocManager,
						restPlacementTabManager: restPlacementTabManager,
						canCreateContact: <?=CUtil::PhpToJSObject($arResult['CAN_CREATE_CONTACT'])?>,
						canCreateCompany: <?=CUtil::PhpToJSObject($arResult['CAN_CREATE_COMPANY'])?>,
						duplicateControl: <?=CUtil::PhpToJSObject($arResult['DUPLICATE_CONTROL'])?>,
						initialMode: "<?=CUtil::JSEscape($arResult['INITIAL_MODE'])?>",
						enableModeToggle: <?=$arResult['ENABLE_MODE_TOGGLE'] ? 'true' : 'false'?>,
						enableVisibilityPolicy: <?=$arResult['ENABLE_VISIBILITY_POLICY'] ? 'true' : 'false'?>,
						isToolPanelAlwaysVisible: <?=$arResult['IS_TOOL_PANEL_ALWAYS_VISIBLE'] ? 'true' : 'false'?>,
						enableToolPanel: <?=$arResult['ENABLE_TOOL_PANEL'] ? 'true' : 'false'?>,
						enableBottomPanel: <?=$arResult['ENABLE_BOTTOM_PANEL'] ? 'true' : 'false'?>,
						enableFieldsContextMenu: <?=$arResult['ENABLE_FIELDS_CONTEXT_MENU'] ? 'true' : 'false'?>,
						enablePageTitleControls: <?=$arResult['ENABLE_PAGE_TITLE_CONTROLS'] ? 'true' : 'false'?>,
						enableCommunicationControls: <?=$arResult['ENABLE_COMMUNICATION_CONTROLS'] ? 'true' : 'false'?>,
						enableExternalLayoutResolvers: <?=$arResult['ENABLE_EXTERNAL_LAYOUT_RESOLVERS'] ? 'true' : 'false'?>,
						readOnly: <?=$arResult['READ_ONLY'] ? 'true' : 'false'?>,
						enableAjaxForm: <?=$arResult['ENABLE_AJAX_FORM'] ? 'true' : 'false'?>,
						enableRequiredUserFieldCheck: <?=$arResult['ENABLE_REQUIRED_USER_FIELD_CHECK'] ? 'true' : 'false'?>,
						enableSectionEdit: <?=$arResult['ENABLE_SECTION_EDIT'] ? 'true' : 'false'?>,
						enableSectionCreation: <?=$arResult['ENABLE_SECTION_CREATION'] ? 'true' : 'false'?>,
						enableSettingsForAll: <?=$arResult['ENABLE_SETTINGS_FOR_ALL'] ? 'true' : 'false'?>,
						inlineEditLightingHint: "<?=CUtil::JSEscape($arResult['INLINE_EDIT_LIGHTING_HINT'] ?? null)?>",
						inlineEditSpotlightId: "<?=CUtil::JSEscape($arResult['INLINE_EDIT_SPOTLIGHT_ID'] ?? '')?>",
						enableInlineEditSpotlight: <?=$arResult['ENABLE_INLINE_EDIT_SPOTLIGHT'] ? 'true' : 'false'?>,
						containerId: "<?=CUtil::JSEscape($containerID)?>",
						buttonContainerId: "<?=CUtil::JSEscape($buttonContainerID)?>",
						createSectionButtonId: "<?=CUtil::JSEscape($createSectionButtonID)?>",
						configMenuButtonId: "<?=CUtil::JSEscape($configMenuButtonID)?>",
						configIconId: "<?=CUtil::JSEscape($configIconID)?>",
						htmlEditorConfigs: <?=CUtil::PhpToJSObject($htmlEditorConfigs)?>,
						serviceUrl: "<?=CUtil::JSEscape($arResult['SERVICE_URL'])?>",
						externalContextId: "<?=CUtil::JSEscape($arResult['EXTERNAL_CONTEXT_ID'])?>",
						contextId: "<?=CUtil::JSEscape($arResult['CONTEXT_ID'])?>",
						context: <?=CUtil::PhpToJSObject($arResult['CONTEXT'])?>,
						entityDetailsUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_ENTITY_DETAILS'])?>",
						contactCreateUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_CONTACT_CREATE'])?>",
						contactEditUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_CONTACT_EDIT'])?>",
						contactRequisiteSelectUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_CONTACT_REQUISITE_SELECT'])?>",
						companyCreateUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_COMPANY_CREATE'])?>",
						companyEditUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_COMPANY_EDIT'])?>",
						companyRequisiteSelectUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_COMPANY_REQUISITE_SELECT'])?>",
						requisiteEditUrl: "<?=CUtil::JSEscape($arResult['PATH_TO_REQUISITE_EDIT'])?>",
						options: <?=CUtil::PhpToJSObject($arResult['EDITOR_OPTIONS'])?>,
						attributeConfig: <?=CUtil::PhpToJSObject($arResult['ATTRIBUTE_CONFIG'])?>,
						showEmptyFields: <?=$arResult['SHOW_EMPTY_FIELDS'] ? 'true' : 'false'?>,
                        ajaxData: <?=CUtil::PhpToJSObject($arResult['COMPONENT_AJAX_DATA'])?>,
						isEmbedded: <?=$arResult['IS_EMBEDDED'] ? 'true' : 'false'?>,
						customToolPanelButtons: <?=CUtil::PhpToJSObject($arResult['CUSTOM_TOOL_PANEL_BUTTONS'])?>,
						toolPanelButtonsOrder: <?=CUtil::PhpToJSObject($arResult['TOOL_PANEL_BUTTONS_ORDER'])?>,
						restrictions: <?=CUtil::PhpToJSObject($arResult['RESTRICTIONS'])?>
					}
				)
			);
		}
	);



    buttonsDiv=document.getElementById("pagetitle-menu");
    if(buttonsDiv){
        eachButtonContainer=buttonsDiv.children[0];
        if(eachButtonContainer && eachButtonContainer.children){
            for (let i = 0; i < eachButtonContainer.children.length; i++) {

                buttonIds=eachButtonContainer.children[i].getAttribute("id");

                if(buttonIds && buttonIds.includes("document")){
                        // console.log(buttonIds);
                }else if(buttonIds && buttonIds.includes("bp_starter")){
                       
                }else{	
					// console.log(buttonIds);
					buttonIds=eachButtonContainer.children[i].style.setProperty('display', 'none', 'important');
                }
            }
        }
        
    }



</script>


<style>

	table {
	font-family: arial, sans-serif;
	border-collapse: collapse;
	width: 100%;
	}

	td, th {
	border: 1px solid #dddddd;
	text-align: left;
	padding: 8px;
	}

	tr:nth-child(even) {
	background-color: #dddddd;
	}

	.seperator{
		width:100%;
		height:25px;
	}

	.gadaxdebi{
		/* display:flex; */
		align-content:center;
	}

	.crm-entity-section-info{
		display:flex;
		gap:2rem;
	}

</style>






<script>

	pathname 	= window.location.pathname.split("/");

	if(pathname[1] == "crm" && pathname[2] == "deal" && pathname[3] == "details"){
		
    
		let gadaxdeb = <?php echo json_encode($gadaxdeb); ?>;
		let price = <?php echo json_encode($price); ?>;
		let valuta = <?php echo json_encode($valuta); ?>;
		let deal = <?php echo json_encode($deal); ?>;
		let currentStage = <?php echo json_encode($currentStage); ?>;

		if(!currentStage){
			currentStage = "new";
		}
		
		stageArr=currentStage.split(":");

		if(stageArr[0] !="C1"){
			let gadaxdebiDIV=
			`<div class="gadaxdebi" id="gadaxdebiDiv">
				<h1>გადახდები</h1>
				<div class = "seperator"></div>
				<div  onclick="showAddPayment();" class="webform-small-button webform-small-button-transparent" style="background-color:silver">გადახდის დამატება</div>
				<div class = "seperator"></div>
				<div id="gadaxdebiTable"></div>
				<div class = "seperator"></div>
				<div id="darcheniliGadaxda"></div>
			</div>`;


			// Get the current date
			today = new Date();

			// Extract day, month, and year components
			day = today.getDate();
			month = today.getMonth() + 1; // Note: Months are zero-based
			year = today.getFullYear();

			// Format day and month with leading zeros if needed
			day = (day < 10) ? '0' + day : day;
			month = (month < 10) ? '0' + month : month;

			// Create the formatted date string
			formattedDate = year + '-' + month + '-' + day;

				
			let addPaymentContainer = `
			<div id="addPaymentContainer" style="position: absolute; left: 25%; top: 15%; z-index: 1000; width: 600px; padding: 0 20px 0; background-color: #fff;">
					<div style="height: 49px;">
						<span class="popup-window-titlebar-text">გადახდის დამატება</span>
					</div>
					<div style="overflow-x: auto; padding: 20px; background-color: #eef2f4;">
						<div>
							<div class="bizproc-item bizproc-workflow-template" style="border: 1px solid #D8D8D8; padding: 0 1.5em 1.5em 1em;">
								<span class="bizproc-item-legend bizproc-workflow-template-title" style="padding: 0 1em; margin-left: 2em; font-size: 110%; color: #000000; position: absolute; top: 61px; background: #eef2f4;">გადახდის დამატება</span>
								<div class="bizproc-modern-type-control-container" style="margin: 10px 0 17px 0; position: relative;">
									
								<div   id="priceDiv" class="bizproc-modern-type-control-container" style="margin: 10px 0 17px 0; position: relative;">
									<span style="display: block; margin: 0 0 15px 0; font-size: 13px; color: #80868e;">
										თარიღი:
									</span>
									<div>
										<input id="paymentDate" class="bizproc-type-control bizproc-type-control-double" style="width: 100%; height: 36px;" type="date"; value=${formattedDate} />
									</div>
								</div>	
								<div   id="priceDivGel" class="bizproc-modern-type-control-container" style="margin: 10px 0 17px 0; position: relative;">
									<span style="display: block; margin: 0 0 15px 0; font-size: 13px; color: #80868e;">
										თანხა: ${valuta}
									</span>
									<div>
										<input id="paymentValue" class="bizproc-type-control bizproc-type-control-double" style="width: 100%; height: 36px;" type="number" />
									</div>
								</div>	
								</div>
							</div>
						</div>
						<div id="prodChangeSuccessBlock" style="display: none; color: green; text-align: center; margin: 15px 0 5px; font-size: 16px;">თქვენი მოთხოვნა გაგზავნილია</div>
						<div id="prodChangeFailBlock" style="display: none; color: red; text-align: center; margin: 15px 0 5px; font-size: 16px;">მოთხვონის დროს დაფიქსირდა შეცდომა</div>
						<div id="addPaymentWarningBlock" style="display: none; color: red; text-align: center; margin: 15px 0 5px; font-size: 16px;">გთხოვთ შეავსოთ ყველა ველი</div>
					</div>
					<span onclick="removeAddPayment();" class="popup-window-close-icon popup-window-titlebar-close-icon"></span>
					<div style="text-align: center; padding: 20px 0 10px; position: relative;">
						<span id="saveAddPaymentBtn" onclick="saveAddPayment()" class="popup-window-button" style="background: #bbed21;-webkit-box-shadow: none; box-shadow: none; color: #535c69;">შენახვა</span>
						<span onclick="removeAddPayment();" class="popup-window-button" style="margin-right: 0; color: #f1361b; border-bottom-color: #ffb4a9">გაუქმება</span>
					</div>
				</div>
			`;



		


			let gadaxdaTable='';
			let darcheniliGadaxda='';

			if(gadaxdeb && gadaxdeb.length>0){
				gadaxdaTable+=`<table>
					<tr>
						<th>თარიღი</th>
						<th>თანხა (${valuta})</th>
					</tr>
				`;
				
				for (let i = 0; i < gadaxdeb.length; i++) {

					gadaxdaTable+=`
					<tr>
						<td>${gadaxdeb[i]["DATE"]}</td>
						<td>${gadaxdeb[i]["MONEY"]}</td>
					</tr>`;
				
					
				}

				gadaxdaTable+=`</table>`;
			}

			mainDiv =document.querySelector('[data-tab-id="main"]');
			createdGadaxdebiDiv=document.getElementById("gadaxdebiDiv");
			
			if(mainDiv && !createdGadaxdebiDiv){
				mainDiv.innerHTML+=`${gadaxdebiDIV}`;
				
			}
			
			gadaxdebiTableDiv =document.getElementById("gadaxdebiTable"); 
			if(gadaxdebiTableDiv){
				gadaxdebiTableDiv.innerHTML=gadaxdaTable;
			}



			function showAddPayment() {
				let templateContainer = document.querySelector(".template-bitrix24");
				$(templateContainer).append(addPaymentContainer);   
			}


			function removeAddPayment() {
				let addPaymentContainer = document.getElementById("addPaymentContainer");
				let templateContainer = document.querySelector(".template-bitrix24");

				templateContainer.removeChild(addPaymentContainer);
			}


			function saveAddPayment() {
					
				let paymentDate = document.getElementById("paymentDate").value;
				let paymentValue = document.getElementById("paymentValue").value;

				if(!paymentDate || !paymentValue){
					let error = document.getElementById("addPaymentWarningBlock").style.display="block";
				}

				console.log(paymentDate);
				console.log(paymentValue);
				if(paymentDate && paymentValue) {

					let params = {};

					params["paymentDate"] = paymentDate;
					params["paymentValue"] = paymentValue;
					params["dealID"]=pathname[4];
					

					console.log(params);
				
					post_fetch(`${location.origin}/rest/local/createPayment.php`, {"params":params})
					.then(data => {
						return data.json();
					})
					.then(data => {
						console.log(data);
					})
					.catch(err => {
						console.log(err);
					});
					

					location.href=`http://213.131.35.178:62100/crm/deal/details/${pathname[4]}/`;
					
				}
			}
		}



		

		setInterval(() => {
			addButton =document.querySelector('[data-tab-id="tab_lists_18"]').children[0]; 
			if(addButton){
				addButton.style.display="none";
			}
		}, 100);


		

				
	

		

		if(currentStage == "WON"){
			let invoicePopupInterval = setInterval(() => {

				docPopupID=`popup-window-content-menu-popup-toolbar_deal_details_${pathname[4]}_document_menu`;
				
				popupDiv=document.getElementById(docPopupID);
				if(popupDiv){
					clearInterval(invoicePopupInterval);
					for (let i = 0; i < popupDiv.children[0].children[0].children.length; i++) {
						if(popupDiv.children[0].children[0].children[i].children[1]){
							if(popupDiv.children[0].children[0].children[i].children[1].textContent !="Documents"){
								popupDiv.children[0].children[0].children[i].style.display="none";
							}
						}		
					}
				}
			}, 100);
		}else{

			let InvoicePopupByValuta = setInterval(() => {

			docPopupID=`popup-window-content-menu-popup-toolbar_deal_details_${pathname[4]}_document_menu`;

			popupDiv=document.getElementById(docPopupID);
			if(popupDiv){
				clearInterval(InvoicePopupByValuta);
				for (let i = 0; i < popupDiv.children[0].children[0].children.length; i++) {
					if(popupDiv.children[0].children[0].children[i].children[1]){
						docName=popupDiv.children[0].children[0].children[i].children[1].textContent;
						if(price > 0){
							if(valuta == "USD"){ // დოლარი
								if(docName !="Documents" && docName !="TBC invoice - აშშ" && docName !="BOG Invoice - აშშ" && docName !="ხელშეკრულება" && docName !="ხელშეკრულება   LTD" && docName !="ხელშეკრულება FORTUNER" ){
									popupDiv.children[0].children[0].children[i].style.display="none";
								}
					
							}else if(valuta == "GEL"){ // ლარი
								if(docName !="Documents" && docName !="TBC invoice" && docName !="BOG Invoice" && docName !="ხელშეკრულება" && docName !="ხელშეკრულება   LTD" && docName !="ხელშეკრულება FORTUNER" ){
									popupDiv.children[0].children[0].children[i].style.display="none";
								}
							}
						}else{
							if(docName !="Documents" ){
								popupDiv.children[0].children[0].children[i].style.display="none";
							}
						}
						
					}		
				}
			}
			}, 200);
			
		}
		
		
		
		rightSide =document.querySelector(".crm-entity-stream-container"); 
		if(rightSide){
			// rightSide.style.display="none";
		}




		if(price || price==0){



			price = Number(price)

			let priceFormated = price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

			if(valuta){

				darcheniliGadaxda=`<p>სულ გადასახდელი: ${priceFormated}  ${valuta}</p>`;

			}else{
				darcheniliGadaxda=`<p>სულ გადასახდელი: ${priceFormated}  </p>`;
				
			}



			if(gadaxdeb && gadaxdeb.length>0){
				ukveGadaxdili=0;
				for (let i = 0; i < gadaxdeb.length; i++) {
					ukveGadaxdili+=Number(gadaxdeb[i]["MONEY"]);
				}
				darchenili=price-ukveGadaxdili;				
				let darcheniliFormated = darchenili.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

			

				darcheniliGadaxda+=`<p>დარჩენილი გადახდა: ${darcheniliFormated}  ${valuta}</p>`;
			}
		}


		darhceniliGadaxdaDiv =document.getElementById("darcheniliGadaxda"); 
		if(darhceniliGadaxdaDiv){
			darhceniliGadaxdaDiv.innerHTML=darcheniliGadaxda;
		}
		






		
		////// ბიზნეს პროცესები //////////////////////////


		recomendPrice = <?php echo json_encode($recomendPrice); ?>;

		setInterval(() => {

			let runWorkflowPopup=document.getElementById("bp-starter-parameters-popup-1");

			if(runWorkflowPopup){

				workflowName = runWorkflowPopup.children[0].children[0].textContent;

				if(workflowName == "გარიგების ფასის განსაზღვრა"){
					runWorkflowPopup.children[0].children[0].innerHTML = `<span>${workflowName} <span style="color:red;">სარეკომენდაციო ფასი: ${recomendPrice}</span></span>`;		
				}

				if (workflowName.includes( "გარიგების ფასის განსაზღვრა")){
					workflowPopContent=runWorkflowPopup.children[1].children[0].children[11].children[0].children[5];

					archeuliValuta=workflowPopContent.children[1].children[1].children[0].value;

					if(!archeuliValuta){
						workflowPopContent.children[2].style.display="none";
						workflowPopContent.children[3].style.display="none";
						workflowPopContent.children[4].style.display="none";
					}else if (archeuliValuta == "ლარი"){
						workflowPopContent.children[2].style.display="";
						workflowPopContent.children[3].style.display="";
						workflowPopContent.children[4].style.display="none";
					}else if (archeuliValuta == "დოლარი"){
						workflowPopContent.children[2].style.display="";
						workflowPopContent.children[3].style.display="none";
						workflowPopContent.children[4].style.display="";
					}
				
					console.log(archeuliValuta)

					// for (let i = 1; i < workflowPopContent.children.length; i++) {
					// 	console.log(workflowPopContent.children[i].children[1].children[0].value);
						
					// }
				}
			}

		}, 400);

			

		if(!price){
			let workflowButtons = setInterval(() => {

				workflowPopup=document.getElementById("popup-window-content-menu-popup-bp-starter-tpl-menu-1");
				if(workflowPopup){
					clearInterval(workflowButtons);
					workflowPopup.children[0].children[0].children[1].style.display="none";
					// console.log(workflowPopup.children[0].children[0].children[1]);
				}
			}, 200);
		}


		////// დილის ველები  //////////////////


		setInterval(() => {

			let opportunityField=document.querySelector('[data-cid="OPPORTUNITY_WITH_CURRENCY"]');		
            if(opportunityField){
                opportunityField.parentElement.style.pointerEvents="none";
            }



			tanxaLari=document.querySelector('[data-cid="UF_CRM_1701416970265"]');

			tanxaLariValue = <?echo $priceLari ;?>;

			// tanxaLariValue = tanxaLari.children[3].children[0].children[0].textContent;
			// tanxaLariValue = tanxaLariValue.replace("₾", "");

			tanxaLari.children[3].children[0].innerHTML=`<span style="color:#80868e; font-size:24px; line-height: 30px;font-family: var(--ui-font-family-secondary,var(--ui-font-family-open-sans)); font-weight: var(--ui-font-weight-regular,400);">₾ </span><span style="font-size:36px; ">${tanxaLariValue.toLocaleString()}</span>`;
	

			let recivePaymentButton=document.querySelector('.crm-entity-widget-content-block-inner-pay-button');
	
			if(recivePaymentButton){
				recivePaymentButton.style.display="none";
			}
	


		}, 700);


	 

	



		/////// სავალდებულო ველების ფორმატები //////



		setInterval(() => {
			saveBtn=document.querySelector(".crm-entity-popup-fill-required-fields-btns");
			if(saveBtn){

				pnAutirisation = "YES";
				mailAutorisation = "YES";

				for (let i = 0; i < saveBtn.parentElement.children[0].children[12].children[0].children[5].children[0].children[1].children.length; i++) {

					inputsName = saveBtn.parentElement.children[0].children[12].children[0].children[5].children[0].children[1].children[i].children[1].children[0].innerText;

					if(inputsName == "პირადი ნომერი"){
						pnAutirisation = "NO";
						pnInput=saveBtn.parentElement.children[0].children[12].children[0].children[5].children[0].children[1].children[i].children[2].children[0].children[0].children[0].value;

						if(pnInput.length == 11 && Number(pnInput)){
							pnAutirisation = "YES";
						}

					}else if(inputsName == "Mail"){
						mailAutorisation = "NO";
						mailInpit=saveBtn.parentElement.children[0].children[12].children[0].children[5].children[0].children[1].children[i].children[2].children[0].children[0].children[0].value;
						emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
						let isValidEmail1 = emailRegex.test(mailInpit);
						if(isValidEmail1){
							mailAutorisation = "YES";
						}
					}
			
					
				}
				// pnInput=saveBtn.parentElement.children[0].children[12].children[0].children[5].children[0].children[1].children[0].children[2].children[0].children[0].children[0].value;
				// mailInpit=saveBtn.parentElement.children[0].children[12].children[0].children[5].children[0].children[1].children[2].children[2].children[0].children[0].children[0].value;


				if(pnAutirisation == "YES" && mailAutorisation=="YES"){
					saveBtn.children[0].style.display="";				
				}else{
					saveBtn.children[0].style.display="none";
				}

			}

		}, 420);

	}
    //////////////////////////////////////////////
	


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
