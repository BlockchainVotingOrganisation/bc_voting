<?php
/**
 * Rev.128
 */
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_bcvoting_domain_model_project'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_bcvoting_domain_model_project']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, password, reference, logo, category, description, start, end, ballots, infosite, forum_url, blockchain_name, rpc_server, rpc_password, rpc_user, rpc_port, assignments, wallet_address',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, password, reference, logo, category, description, start, end, ballots, infosite, forum_url, blockchain_name, rpc_server, rpc_password, rpc_user, rpc_port, assignments, wallet_address, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'renderType' => 'selectSingle',
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
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_bcvoting_domain_model_project',
				'foreign_table_where' => 'AND tx_bcvoting_domain_model_project.pid=###CURRENT_PID### AND tx_bcvoting_domain_model_project.sys_language_uid IN (-1,0)',
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

		'name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),

		'password' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.password',
				'config' => array(
						'type' => 'input',
						'size' => 15,
						'eval' => 'trim'
				),
		),
			
		'reference' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.reference',
				'config' => array(
						'type' => 'input',
						'size' => 15,
						'eval' => 'trim'
				),
		),

		'logo' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.logo',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image', array(
				'appearance' => array(
					'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
				),
				'maxitems' => 1,
				// custom configuration for displaying fields in the overlay/reference table
				// to use the imageoverlayPalette instead of the basicoverlayPalette
				'foreign_match_fields' => array(
					'fieldname' => 'logo',
					'tablenames' => 'tx_bcvoting_domain_model_project',
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
			
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.description',
			'config' => array(
				'type' => 'text',
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
		'assignments' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.assignments',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_bcvoting_domain_model_assignment',
				'foreign_field' => 'project',
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
		'ballots' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.ballots',
				'config' => array(
						'type' => 'inline',
						'foreign_table' => 'tx_bcvoting_domain_model_ballot',
						'foreign_field' => 'project',
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

		'blockchain_name' => array(
				'exclude' => 1,
				'label' => 'Blockchain name',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'trim'
				),
		),	
		'rpc_server' => array(
				'exclude' => 1,
				'label' => 'RPC-Server',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'trim'
				),
		),	
		'rpc_user' => array(
				'exclude' => 1,
				'label' => 'RPC-User',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'trim'
				),
		),
		'rpc_password' => array(
				'exclude' => 1,
				'label' => 'RPC-Password',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'eval' => 'trim'
				),
		),
		'rpc_port' => array(
				'exclude' => 1,
				'label' => 'RPC-Port',
				'config' => array(
						'type' => 'input',
						'size' => 5,
						'eval' => 'integer'
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
		'category' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.category',
				'config' => array(
						'type' => 'select',
						'renderType' => 'selectSingle',
						'foreign_table' => 'tx_bcvoting_domain_model_category',
						'minitems' => 0,
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
		'infosite' => array(
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.infosite',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'max' => 255,
				)
		),
			
		'forum_url' => array(
				'label' => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_project.forumUrl',
				'config' => array(
						'type' => 'input',
						'size' => 30,
						'max' => 255,
				)
		),
	),
);
?>