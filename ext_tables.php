<?php
/**
 * Rev.150
 */
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
// 	$_EXTKEY,
// 	'Project',
// 	'BVS-Project'
// );

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY,
		'BVS_newProjectWizard',
		'BVS New Project Wizard'
		);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY,
		'BVS_Main',
		'BVS Main PlugIn'
		);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY,
		'BVS_Admin',
		'BVS Admin'
		);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY,
		'BVS_Evaluation',
		'BVS Evaluation'
		);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY,
		'BVS_Office',
		'BVS Office'
		);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY,
		'BVS_Wallet',
		'BVS Wallet'
		);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		$_EXTKEY,
		'BVS_Keys',
		'BVS PKI'
		);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_assignment', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_assignment.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_assignment');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'bc_voting');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_project', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_project.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_project');

# Asset
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_asset', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_asset.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_asset');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_project', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_ballot.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_ballot');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_option', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_option.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_option');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_argument', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_argument.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_argument');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_voting', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_voting.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_voting');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_category', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_category.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_category');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bcvoting_domain_model_role', 'EXT:bc_voting/Resources/Private/Language/locallang_csh_tx_bcvoting_domain_model_role.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bcvoting_domain_model_role');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', $GLOBALS['TCA']['fe_users']['ctrl']['type'],'','after:' . $TCA['fe_users']['ctrl']['label']);

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY));
$pluginName = strtolower('BVS_Main');
$pluginSignature = $extensionName.'_'.$pluginName;
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY . '/Configuration/FlexForms/ProjectConfiguration.xml');

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY));
$pluginName = strtolower('BVS_Admin');
$pluginSignature = $extensionName.'_'.$pluginName;
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY . '/Configuration/FlexForms/ProjectConfiguration.xml');

$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY));
$pluginName = strtolower('BVS_newProjectWizard');
$pluginSignature = $extensionName.'_'.$pluginName;
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY . '/Configuration/FlexForms/ProjectWizardConfiguration.xml');
?>