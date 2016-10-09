<?php
// Rev. 109
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Goettertz.' . $_EXTKEY,
	'Project',
	array(
 		'Project' => 'list, show, new, create, edit, editbcparams, update, delete, assign, evaluation, arguments, settings, createSettings, removeLogo, checkVoting, execute',
 		'Option' => 'list, show, new, create, edit, update, delete, vote, removeLogo',
 		'Wallet' => 'show, sendAssets',
 		'User' => 'list, show, new, create, edit, update, delete, import, upload',
		'Blockchain' => 'show',
		'Argument' => 'list, show, new, create, edit, update, delete',
		'Ballot' => 'list, show, new, create, edit, update, delete, sealBallot, vote, removeLogo',
	),
	// non-cacheable actions
	array(
		'Project' => 'list, show, new, create, edit, editbcparams, update, delete, assign, evaluation, arguments, settings, createSettings, removeLogo, checkVoting, execute',
		'Option' => 'list, show, new, create, edit, update, delete, vote, removeLogo',
		'Wallet' => 'show, sendAssets',
 		'User' => 'list, show, new, create, edit, update, delete, import, upload',
		'Blockchain' => 'show',
		'Argument' => 'list, show, new, create, edit, update, delete',
		'Ballot' => 'list, show, new, create, edit, update, delete, sealBallot, vote, removeLogo',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('Goettertz\\BcVoting\\Property\\TypeConverter\\UploadedFileReferenceConverter');
// \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('Goettertz\\BcVoting\\Property\\TypeConverter\\ObjectStorageConverter');
		
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
				'Goettertz.' . $_EXTKEY,
				'Bvs_nav1',
				array('Project' => 'nav1'),
				array('Project' => 'nav1')
);


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		'Goettertz.' . $_EXTKEY,
		'BVS_newProjectWizard',
		array(
				'Project' => 'new, create, createSettings, update'
		),
		array(
				'Project' => 'new, create, createSettings, update'
		)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		'Goettertz.' . $_EXTKEY,
		'BVS_personalWallet',
		array(
				
				'Wallet' => 'show, send'
		),
		array(
				
				'Wallet' => 'show, send'
		)
);
?>
