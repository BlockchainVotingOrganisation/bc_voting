<?php
/**
 * Rev.74
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_bcvoting_domain_model_asset'] = array(
	'ctrl' => array(
			'title'	=> 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_asset',
			'label' => 'name',
			'tstamp' => 'tstamp',
			'crdate' => 'crdate',
			'cruser_id' => 'cruser_id',
			'dividers2tabs' => TRUE,

			'versioningWS' => 2,
			'versioning_followPages' => TRUE,

			'languageField' => 'sys_language_uid',
			'transOrigPointerField' => 'l10n_parent',
			'transOrigDiffSourceField' => 'l10n_diffsource',
			'delete' => 'deleted',
			'enablecolumns' => array(
					'disabled' => 'hidden'
			),
			'searchFields' => 'name',
			// 				'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Asset.php',
			//'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_bcvoting_domain_model_project.gif'
	),
	
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime, name, assetId, quantity, options, reference, project, start, end',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, start, end, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
	
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_bcvoting_domain_model_asset',
				'foreign_table_where' => 'AND tx_bcvoting_domain_model_asset.pid=###CURRENT_PID### AND tx_bcvoting_domain_model_asset.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),	

		'name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_asset.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
			
			
		'assetId' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_asset.asset_id',
			'config' => array(
				'type' => 'text',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'quantity' => array(
					'exclude' => 1,
					'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_asset.quantity',
					'config' => array(
							'type' => 'text',
							'size' => 30,
							'eval' => 'integer'
					),
		),
				
		'divisibility' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_asset.divisibility',
				'config' => array(
						'type' => 'text',
						'size' => 30,
						'eval' => 'integer'
				),
		),

	),
);
?>