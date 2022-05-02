<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteProjectActivityAction
 *
 * @author orangehrm
 */
class deleteProjectActivityAction extends sfAction {

	private $projectService;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}

	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {

//            echo "<pre>";print_r($_POST);exit;
//            jugni have made changes 20130625
               $allactivity=$request->getParameter('chkSelectRow');
		$toBeDeletedActivityIds =explode(",",$allactivity);
            
//                print_r($toBeDeletedActivityIds);exit;
                
		$projectId = $request->getParameter('projectId');
//		echo $projectId;exit;
		if (!empty($toBeDeletedActivityIds)) {
//                    echo "in if";
			$delete = true;
			foreach ($toBeDeletedActivityIds as $toBeDeletedActivityId) {
                            
				$deletable = $this->getProjectService()->hasActivityGotTimesheetItems($toBeDeletedActivityId);
				if ($deletable) {
					$delete = false;
					break;
				}
			}
//                        echo $delete;
			if ($delete) {
				foreach ($toBeDeletedActivityIds as $toBeDeletedActivityId) {
//                                        echo "in foreach";
					$customer = $this->getProjectService()->deleteProjectActivities($toBeDeletedActivityId);
				}
				$this->getUser()->setFlash('templateMessageAct', array('success', __(TopLevelMessages::DELETE_SUCCESS)));
			} else {
				$this->getUser()->setFlash('templateMessageAct', array('failure', __('Not Allowed to Delete Project Activites Which Have Time Logged Against')));
			}
//                        exit;
		}

		$this->redirect('admin/saveProject?projectId='.$projectId);
	}

}

?>