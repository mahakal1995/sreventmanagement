<?php

// kartik new created for diu

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

class AddProjectDiuForm extends BaseForm {
	
	private $projectService;
	public $edited = false;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}
	
	public function configure() {

		$this->setWidgets(array(
		    'projectId' => new sfWidgetFormInputHidden(),
		    'diuId' => new sfWidgetFormInputHidden(),                    
		    'diuName' => new sfWidgetFormInputText(),                    		    
		));

		$this->setValidators(array(
		    'projectId' => new sfValidatorNumber(array('required' => true)),
		    'diuId' => new sfValidatorNumber(array('required' => false)),                   
		    'diuName' => new sfValidatorString(array('required' => true, 'max_length' => 102)),                    		    
		));

		$this->widgetSchema->setNameFormat('addProjectDiu[%s]');

	}
	
	public function save(){
           
		
		$projectId = $this->getValue('projectId');
		$diuId = $this->getValue('diuId');	
		if(!empty ($diuId)){
			$diu = $this->getProjectService()->getProjectDiuById($diuId);
			$this->edited = true;
		} else {
			$diu = new ProjectDiu();
		}
		
		$diu->setProjectId($projectId);
		$diu->setName($this->getValue('diuName'));                
		$diu->setIsDeleted(ProjectDiu::ACTIVE_PROJECT);
		$diu->save();
		return $projectId;
	}

}

?>
 
