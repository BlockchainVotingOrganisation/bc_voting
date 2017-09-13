<?php
if (!isset($GLOBALS['TCA']['fe_users']['ctrl']['type'])) {
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['fe_users']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumns = array();
	$tempColumns[$GLOBALS['TCA']['fe_users']['ctrl']['type']] = array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting.tx_extbase_type',
			'config' => array(
					'type' => 'select',
					'items' => array(),
					'size' => 1,
					'maxitems' => 1,
			)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns, 1);
}
$GLOBALS['TCA']['fe_users']['columns'][$TCA['fe_users']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:fe_users.tx_extbase_type.Tx_BcVoting_User','Tx_BcVoting_User');
$GLOBALS['TCA']['fe_users']['types']['Tx_BcVoting_User']['showitem'] = $TCA['fe_users']['types']['0']['showitem'];
$GLOBALS['TCA']['fe_users']['types']['Tx_BcVoting_User']['showitem'] .= ',--div--;LLL:EXT:bc_voting/Resources/Private/Language/locallang_db.xlf:tx_bcvoting_domain_model_user,';

?>