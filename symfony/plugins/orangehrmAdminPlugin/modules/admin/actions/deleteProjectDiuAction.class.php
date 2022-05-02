<?php
// kartik new created for diu

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of deleteProjectDiuAction
 *
 * @author orangehrm
 */
class deleteProjectDiuAction extends sfAction {

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

		$toBeDeletedDiuIds = $request->getParameter('chkSelectRow');
		$projectId = $request->getParameter('projectId');		
		if (!empty($toBeDeletedDiuIds)) {
			$delete = true;
			if ($delete) {
				foreach ($toBeDeletedDiuIds as $toBeDeletedDiuId) {

					$customer = $this->getProjectService()->deleteProjectDius($toBeDeletedDiuId);
				}
				$this->getUser()->setFlash('templateMessageAct', array('success', __(TopLevelMessages::DELETE_SUCCESS)));
			} else {
				$this->getUser()->setFlash('templateMessageAct', array('failure', __('Not Allowed to Delete Project Diu Which Have Time Logged Against')));
			}
		}
		$this->redirect('admin/saveProject?projectId='.$projectId);
	}

}
?>
