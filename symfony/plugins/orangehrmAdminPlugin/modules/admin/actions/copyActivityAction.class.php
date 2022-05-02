<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
class copyActivityAction extends sfAction {

	private $projectService;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}

	/**
	 * @param sfForm $form
	 * @return
	 */
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}

	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {

            
		$this->setForm(new CopyActivityForm());
		$projectId = $request->getParameter('projectId');
		$this->form->bind($request->getParameter($this->form->getName()));

		$projectActivityList = $this->getProjectService()->getActivityListByProjectId($projectId);
		if ($this->form->isValid()) {
			$activityNameList = $request->getParameter('activityNames', array());
                        $activity_code = $request->getParameter('activity_code', array());
                        $estimate_time = $request->getParameter('estimate_time', array());
			$activities = new Doctrine_Collection('ProjectActivity');

                    
                   
			$isUnique = true;
			foreach ($activityNameList as $activityName) {
                             $exp_arr=explode("_",$activityName);
                              $name=$exp_arr[0];
				foreach ($projectActivityList as $projectActivity) {
					if (strtolower($name) == strtolower($projectActivity->getName())) {
						$isUnique = false;
						break;
					}
				}
			}
                       
			if ($isUnique) {
				foreach ($activityNameList as $key=> $activityName) {
                                        $exp_arr=explode("_",$activityName);
                                        $name=$exp_arr[0];
                                        $ind=$exp_arr[1];
//                                            echo "key:".$key."   name:".$activityName." Activity_code::".$activity_code[$key]."  estimate_time::".$estimate_time[$key]."<br>";
					$activity = new ProjectActivity();
					$activity->setProjectId($projectId);
					$activity->setName($name);
                                        $activity->setactivity_code($activity_code[$ind]);
                                        $activity->setestimate_time($estimate_time[$ind]);
					$activity->setIsDeleted(ProjectActivity::ACTIVE_PROJECT);
					$activities->add($activity);
				}
				$activities->save();
				$this->getUser()->setFlash('templateMessageAct', array('success', __('Successfully Copied')));
			} else {
				$this->getUser()->setFlash('templateMessageAct', array('failure', __('Name Already Exists')));
			}
			
			$this->redirect('admin/saveProject?projectId=' . $projectId);
		}
	}

}

?>