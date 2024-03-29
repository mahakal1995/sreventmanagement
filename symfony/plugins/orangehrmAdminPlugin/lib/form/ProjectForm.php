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
 *
 */
class ProjectForm extends BaseForm {

	private $customerService;
	public $projectId;
	public $numberOfProjectAdmins = 5;
	public $edited = false;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}

	public function getCustomerService() {
		if (is_null($this->customerService)) {
			$this->customerService = new CustomerService();
			$this->customerService->setCustomerDao(new CustomerDao());
		}
		return $this->customerService;
	}

	public function configure() {

		$this->projectId = $this->getOption('projectId');

		$this->setWidgets(array(
		    'projectId' => new sfWidgetFormInputHidden(),
		    'customerId' => new sfWidgetFormInputHidden(),
		    'customerName' => new sfWidgetFormInputText(),
		    'projectName' => new sfWidgetFormInputText(),
		    'projectAdminList' => new sfWidgetFormInputHidden(),
		    'description' => new sfWidgetFormTextArea(),
		));

		for ($i = 1; $i <= $this->numberOfProjectAdmins; $i++) {
			$this->setWidget('projectAdmin_' . $i, new sfWidgetFormInputText());
		}

		$this->setValidators(array(
		    'projectId' => new sfValidatorNumber(array('required' => false)),
		    'customerId' => new sfValidatorNumber(array('required' => true)),
		    'customerName' => new sfValidatorString(array('required' => true, 'max_length' => 52, 'trim'=>true)),
		    'projectName' => new sfValidatorString(array('required' => true, 'max_length' => 52, 'trim'=>true)),
		    'projectAdminList' => new sfValidatorString(array('required' => false)),
		    'description' => new sfValidatorString(array('required' => false, 'max_length' => 256)),
		));

		for ($i = 1; $i <= $this->numberOfProjectAdmins; $i++) {
			$this->setValidator('projectAdmin_' . $i, new sfValidatorString(array('required' => false, 'max_length' => 100)));
		}

		$this->widgetSchema->setNameFormat('addProject[%s]');

		if ($this->projectId != null) {
			$this->setDefaultValues($this->projectId);
		}
	}

	private function setDefaultValues($projectId) {

		$project = $this->getProjectService()->getProjectById($this->projectId);
		$this->setDefault('projectId', $projectId);
		$this->setDefault('customerId', $project->getCustomer()->getCustomerId());
		$this->setDefault('customerName', $project->getCustomer()->getName());
		$this->setDefault('projectName', $project->getName());
		$this->setDefault('description', $project->getDescription());

		$admins = $project->getProjectAdmin();
		$this->setDefault('projectAdmin_1', $admins[0]->getEmployee()->getFullName());
		for ($i = 1; $i <= count($admins); $i++) {
			$this->setDefault('projectAdmin_' . $i, $admins[$i - 1]->getEmployee()->getFullName());
		}
		$this->setDefault('projectAdminList', count($admins));
	}

	public function save() {

		$id = $this->getValue('projectId');
		if (empty($id)) {

			$project = new Project();
			$projectAdminsArray = $this->getValue('projectAdminList');
			$projectAdmins = explode(",", $projectAdminsArray);
			$projectId = $this->saveProject($project);
			$this->saveProjectAdmins($projectAdmins, $projectId);
		} else {
			$this->edited = true;
			$project = $this->getProjectService()->getProjectById($id);
			$projectId = $this->saveProject($project);
			$projectAdmins = explode(",", $this->getValue('projectAdminList'));
			$existingProjectAdmins = $project->getProjectAdmin();
			$idList = array();
			if ($existingProjectAdmins[0]->getEmpNumber() != "") {
				foreach ($existingProjectAdmins as $existingProjectAdmin) {
					$id = $existingProjectAdmin->getEmpNumber();
					if (!in_array($id, $projectAdmins)) {
						$existingProjectAdmin->delete();
					} else {
						$idList[] = $id;
					}
				}
			}

			$this->resultArray = array();

			$adminList = array_diff($projectAdmins, $idList);
			$newList = array();
			foreach ($adminList as $admin) {
				$newList[] = $admin;
			}
			$projectAdmins = $newList;
			$this->saveProjectAdmins($projectAdmins, $project->getProjectId());
		}
		return $project->getProjectId();
	}

	protected function saveProjectAdmins($projectAdmins, $projectId) {

		if ($projectAdmins[0] != null) {
			for ($i = 0; $i < count($projectAdmins); $i++) {
				$projectAdmin = new ProjectAdmin();
				$projectAdmin->setProjectId($projectId);
				$projectAdmin->setEmpNumber($projectAdmins[$i]);
				$projectAdmin->save();
			}
		}
	}

	protected function saveProject($project) {

		$project->setCustomerId($this->getValue('customerId'));
		$project->setName(trim($this->getValue('projectName')));
		$project->setDescription($this->getValue('description'));
		$project->setIsDeleted(Project::ACTIVE_PROJECT);
		$project->save();
		return $project->getProjectId();
	}

	protected function getCustomerList() {

		$list = array("" => "-- " . __('Select') . " --");
		$customerList = $this->getCustomerService()->getAllCustomers();
		foreach ($customerList as $customer) {

			$list[$customer->getCustomerId()] = $customer->getName();
		}
		return $list;
	}

	public function getEmployeeListAsJson() {

		$jsonArray = array();
		$employeeService = new EmployeeService();
		$employeeService->setEmployeeDao(new EmployeeDao());

        $properties = array("empNumber","firstName", "middleName", "lastName");
        $employeeList = $employeeService->getEmployeePropertyList($properties, 'lastName', 'ASC', true);
        
        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
            
            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }
		$jsonString = json_encode($jsonArray);

		return $jsonString;
	}

	public function getCustomerListAsJson() {

		$jsonArray = array();

		$customerList = $this->getCustomerService()->getAllCustomers(true);


		foreach ($customerList as $customer) {

			$jsonArray[] = array('name' => $customer->getName(), 'id' => $customer->getCustomerId());
		}

		$jsonString = json_encode($jsonArray);

		return $jsonString;
	}


	public function getActivityListAsJson($projectId) {
		$jsonArray = array();

		if (!empty($projectId)) {

			$activityList = $this->getProjectService()->getActivityListByProjectId($projectId);

			foreach ($activityList as $activity) {
				$jsonArray[] = array('name' => $activity->getName(), 'id' => $activity->getActivityId(),'estimate_time'=>$activity->getestimate_time(),'diu_id'=>$activity->getDiuId(),'activity_id'=>$activity->getactivity_code());
//                                $jsonArray[] = array('name' => $activity['name'], 'id' => $activity['activity_id'],'estimate_time'=>$activity['estimate_time'],'diu_id'=>$activity['diu_id']);
			}

			$jsonString = json_encode($jsonArray);
		}
		return $jsonString;
	}
                        
// kartik new created for diu        
        public function getDiuListAsJson($projectId) {

		$jsonArray = array();

		if (!empty($projectId)) {

			$DiuList = $this->getProjectService()->getDiuListByProjectId($projectId);

			foreach ($DiuList as $Diu) {
				$jsonArray[] = array('name' => $Diu->getName());
			}

			$jsonString = json_encode($jsonArray);
		}
		return $jsonString;
	}
        
// kartik new created for diu        

	public function getCustomerProjectListAsJson() {
        $timesheetService = new TimesheetService();
        $timesheetService->setTimesheetDao(new TimesheetDao());
		$jsonArray = array();

		$projectList = $timesheetService->getProjectNameList();


		foreach ($projectList as $project) {
			if ($this->projectId != $project['projectId']) {
				//$jsonArray[] = array('name' => $project['customerName'] . " - ##" . $project['projectName'], 'id' => $project['projectId']);

$jsonArray[] = array('name' => $project['projectName'] . " - ##" . $project['customerName'], 'id' => $project['projectId']);

			}
		}

		$jsonString = json_encode($jsonArray);

		return $jsonString;
	}

}

?>
