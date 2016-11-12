<?php
class Tx_BcVoting_Controller_FrontendUserController extends Tx_Extbase_MVC_Controller_ActionController {
     
    /**
     *
     * @var Tx_MyExt_Service_AccessControlService 
     */
    protected $accessControlService;
   
    /**
     *
     * @param Tx_MyExt_Service_AccessControlService $accessControlService
     */
    public function injectAccessControlService(Tx_MyExt_Service_AccessControlService $accessControlService) {
        $this->accessControlService = $accessControlService;
    }
     
    /**
     *
     * @return void
     */
    public function initializeAction() {
          if(TRUE === $this->accessControllService->hasLoggedInFrontendUser()) {
              // Get all the request arguments                    
              $requestArguments = $this->request->getArguments();
              $requestArguments['frontendUser'] = $this->accessControllService->getFrontendUserUid();
              $this->request->setArguments($requestArguments);
          } else {
             // throw an Exception, do redirect, whatever
          }
    }
     
    /**
     *
     * @param Tx_MyExt_Domain_Model_FrontendUser $frontendUser
     */
    public function showExampleUserAction(Tx_MyExt_Domain_Model_FrontendUser $frontendUser) {
           if($this->accessControllService->isAccessAllowed($frontendUser)) {
               $this->view->assign('frontendUser', $frontendUser);            
           } else {
               // do something
           }
    }
 
    /**
     *
     * @param Tx_MyExt_Domain_Model_FrontendUser $frontendUser
     */
    public function showAllFriendsAction(Tx_MyExt_Domain_Model_FrontendUser $frontendUser) {
           if($this->accessControllService->isAccessAllowed($frontendUser)) {
               $this->view->assign('frontendUser', $frontendUser);            
           } else {
               // do something
           }
    }
}
?>