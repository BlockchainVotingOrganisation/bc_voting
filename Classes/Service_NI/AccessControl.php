<?php
class Tx_MyExt_Service_AccessControlService implements t3lib_Singleton {

	/**
	 * Do we have a logged in feuser
	 * @return boolean
	 */
	public function hasLoggedInFrontendUser() {
		return $GLOBALS['TSFE']->loginUser === 1 ? TRUE : FALSE;
	}

	/**
	 * Get the uid of the current feuser
	 * @return mixed
	 */
	public function getFrontendUserUid() {
		if ($this->hasLoggedInFrontendUser() && !empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
			return intval($GLOBALS['TSFE']->fe_user->user['uid']);
		}
		return NULL;
	}
	 
	/**
	 * @param Tx_Extbase_Domain_Model_FrontendUser $frontendUser
	 * @return boolean
	 */
	public function isAccessAllowed(Tx_Extbase_Domain_Model_FrontendUser $frontendUser) {
		return $this->getFrontendUserUid() === $frontendUser->getUid() ? TRUE : FALSE;
	}
}
?>