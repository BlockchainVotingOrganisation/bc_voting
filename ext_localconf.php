<?php
// Rev. 144
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}
// // Obsolete:
// \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
// 	'Goettertz.' . $_EXTKEY,
// 	'Project',
// 	array(
//  		'Project' => 'admin, new, create, edit, editbcparams, update, delete, assign, evaluation, arguments, createSettings, removeLogo, checkVoting, execute, seal, import',
//  		'Option' => 'list, show, new, create, edit, update, delete, vote, removeLogo',
//  		'Wallet' => 'show, importWallet, import',
//  		'User' => 'list, show, new, create, edit, update, delete, import, upload, sendAssets, getNewAddress, removeAssignment',
// 		'Blockchain' => 'show',
// 		'Argument' => 'list, show, new, create, edit, update, delete',
// 		'Ballot' => 'admin, show, new, create, edit, update, delete, sealBallot, removeLogo',
		
// 	),
// 	// non-cacheable actions
	
// 	array(
// 		'Project' => 'admin, new, create, edit, editbcparams, update, delete, assign, evaluation, arguments, createSettings, removeLogo, checkVoting, execute, seal, import',
// 		'Option' => 'list, show, new, create, edit, update, delete, vote, removeLogo',
// 		'Wallet' => 'show, importWallet, import',
//  		'User' => 'list, show, new, create, edit, update, delete, import, upload, sendAssets, getNewAddress, removeAssignment',
// 		'Blockchain' => 'show',
// 		'Argument' => 'list, show, new, create, edit, update, delete',
// 		'Ballot' => 'admin, show, new, create, edit, update, delete, sealBallot, removeLogo',		
// 	)
	
// );

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('Goettertz\\BcVoting\\Property\\TypeConverter\\UploadedFileReferenceConverter');
		
// \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
// 		'Goettertz.' . $_EXTKEY,
// 		'BVS_newProjectWizard',
// 		array(
// 				'Project' => 'new, create, createSettings',
				
// 		),
// 		array(
// 				'Project' => 'new, create, createSettings',
				
// 		)
// );

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		'Goettertz.' . $_EXTKEY,
		'BVS_Main',
		array(
				'Election' => 'list, show',
				'Ballot' => 'list, show',
				'Voting' => 'create'
		),
		array(
				'Election' => 'list, show',
				'Ballot' => 'list, show',
				'Voting' => 'create'
		)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		'Goettertz.' . $_EXTKEY,
		'BVS_Office',
		array(
				
				'Project' => 'list, show, edit, settings, new, create, update, editbcparams, seal, import, removeLogo',
				'Assignment' => 'list, show, settings, edit, new, create',
				'Ballot' => 'list, show, new, create, edit, update, delete, sealBallot, removeLogo',
				'User' => 'list, show, edit',
				'Option' => 'edit, update',
				'Wallet' => 'show',
				'Pgp' => '',
		),
		array(
				
				'Project' => 'list, show, edit, new, create, update, editbcparams, seal, import, removeLogo',
				'Assignment' => 'list, show, edit, new, create',
				'Ballot' => 'list, show, new, create, edit, update, delete, sealBallot, removeLogo',
				'User' => 'list, show, edit',
				'Option' => 'edit, update',
				'Wallet' => 'show',
				'Pgp' => '',
		)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Goettertz.' . $_EXTKEY,
	'BVS_Evaluation',
	array(
			'Evaluation' => 'list, show, proceed, txlog, streamlog',
			'Transaction' => 'show'
			
	),
	array(
			'Evaluation' => 'list, show, proceed, txlog, streamlog',
			'Transaction' => 'show'
	)
);
?>