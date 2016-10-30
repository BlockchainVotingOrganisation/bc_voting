<?php
/**
 * Rev.86
 * - votes
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_bcvoting_domain_model_ballot'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_bcvoting_domain_model_ballot']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, starttime, endtime, name, logo, description, text, footer, options, reference, votes, project, asset, start, end, wallet_address',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, logo, text, footer, options, reference, votes, project, asset, start, end, wallet_address, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'foreign_table' => 'tx_bcvoting_domain_model_ballot',
				'foreign_table_where' => 'AND tx_bcvoting_domain_model_ballot.pid=###CURRENT_PID### AND tx_bcvoting_domain_model_project.sys_language_uid IN (-1,0)',
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
				'size' => 15,
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
				'size' => 15,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'reference' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_ballot.reference',
				'config' => array(
						'type' => 'input',
						'size' => 15,
						'eval' => 'trim'
				),
		),
		'votes' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_ballot.votes',
				'config' => array(
						'type' => 'input',
						'size' => 2,
						'eval' => 'trim'
				),
		),
		'project' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_ballot.project',
				'config' => array(
						'type' => 'select',
						'renderType' => 'selectSingle',
						'foreign_table' => 'tx_bcvoting_domain_model_project',
						'minitems' => 0,
						'maxitems' => 1000,
						'items' => Array (
								Array("",0),
								),
						'appearance' => array(
								'collapseAll' => 0,
								'levelLinksPosition' => 'top',
								'showSynchronizationLink' => 1,
								'showPossibleLocalizationRecords' => 1,
								'showAllLocalizationLink' => 1
						),
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
		'asset' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_ballot.asset',
				'config' => array(
						'type' => 'input',
						'size' => 25,
						'eval' => 'trim'
				),
		),			
		'name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_ballot.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
			
		'logo' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:upload_example/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_ballot.logo',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image', array(
				'appearance' => array(
					'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
				),
				'maxitems' => 1,
				// custom configuration for displaying fields in the overlay/reference table
				// to use the imageoverlayPalette instead of the basicoverlayPalette
				'foreign_match_fields' => array(
					'fieldname' => 'logo',
					'tablenames' => 'tx_bcvoting_domain_model_ballot',
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
			
		'text' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.text',
			'config' => array(
				'type' => 'text',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'footer' => array(
					'exclude' => 1,
					'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.footer',
					'config' => array(
							'type' => 'text',
							'size' => 30,
							'eval' => 'trim'
					),
		),
				
		'start' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.start',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'datetime',
						'checkbox' => 0,
						'default' => 0,
						'range' => array(
								'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
						),
				),
		),

		'end' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.end',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'datetime',
						'checkbox' => 0,
						'default' => 0,
						'range' => array(
								'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
						),
				),
		),			

		'options' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.options',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_bcvoting_domain_model_option',
				'foreign_field' => 'ballot',
				'minitems'      => 0,
				'maxitems'      => 9999,
				'appearance' => array(
					'collapseAll' => 1,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),
	),
);
?>