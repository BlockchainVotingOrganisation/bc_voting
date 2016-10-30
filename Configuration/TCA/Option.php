<?php
# Revision 92

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_bcvoting_domain_model_option'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_bcvoting_domain_model_option']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, parent, name, description, wallet_address, ballot, logo, color',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, parent, name, description, wallet_address, ballot, logo, color, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'foreign_table' => 'tx_bcvoting_domain_model_option',
				'foreign_table_where' => 'AND tx_bcvoting_domain_model_option.pid=###CURRENT_PID### AND tx_bcvoting_domain_model_option.sys_language_uid IN (-1,0)',
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
	
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'parent' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_option.parent',
				'config' => array(
						'type' => 'select',
						'renderType' => 'selectSingle',
						'foreign_table' => 'tx_bcvoting_domain_model_option',
						'items' => Array (
								Array("",0),
								),
						'minitems' => 1,
						'maxitems' => 1,
						'appearance' => array(
								'collapseAll' => 0,
								'levelLinksPosition' => 'top',
								'showSynchronizationLink' => 1,
								'showPossibleLocalizationRecords' => 1,
								'showAllLocalizationLink' => 1
						),
				),
		),
		'name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_option.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'required,trim'
			),
		),
		'description' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_option.description',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'required,trim'
				),
		),
		'color' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_option.color',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'trim'
				),
		),
		'wallet_address' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_option.wallet_address',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'ballot' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_option.ballot',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_bcvoting_domain_model_ballot',
				'items' => Array (
						Array("",0),
						),
				'minitems' => 1,
				'maxitems' => 1,
				'appearance' => array(
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),
		'logo' => array(
				'exclude' => 0,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_option.logo',
				'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image', array(
						'appearance' => array(
								'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
						),
						'maxitems' => 1,
						// custom configuration for displaying fields in the overlay/reference table
						// to use the imageoverlayPalette instead of the basicoverlayPalette
						'foreign_match_fields' => array(
								'fieldname' => 'logo',
								'tablenames' => 'tx_bcvoting_domain_model_option',
								'table_local' => 'sys_file',
						),
						'foreign_types' => array(
								'0' => array(
										'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
								),
								\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
										'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
								),
								\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
										'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
								),
								\TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
										'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
								),
								\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
										'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
								),
								\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
										'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
						--palette--;;filePalette'
								)
						)
				), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'])
		),
	),
);
?>