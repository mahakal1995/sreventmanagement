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

class AddProjectActivityForm extends BaseForm {
	
	private $projectService;
	public $edited = false;
//add estimate_time jaydeep
	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}
	
	public function configure() {

                $dius = $this->_getDius();
                
//                echo"<pre>";print_r($dius);echo"</pre>";exit;
                
		$this->setWidgets(array(
		    'projectId' => new sfWidgetFormInputHidden(),
		    'activityId' => new sfWidgetFormInputHidden(),
                    'estimate_time2' => new sfWidgetFormInputHidden(),
		    'activityName' => new sfWidgetFormInputText(),
                    'estimate_time' => new sfWidgetFormInputText(),
                    
                    //kartik for add field in activity        
                    'diuId' => new sfWidgetFormSelect(array('choices' => $dius)), // sub division name (not used)                    
                    //kartik for add field in activity        

//Name:sangani jagruti
//Date:2014-03-21    
//Purpose:Add new activity_code                    
                     'activity_code' => new sfWidgetFormInputText(),
		    
		));  
		$this->setValidators(array(
		    'projectId' => new sfValidatorNumber(array('required' => true)),
		    'activityId' => new sfValidatorNumber(array('required' => false)),
                    'estimate_time2' => new sfValidatorNumber(array('required' => false)),
		    'activityName' => new sfValidatorString(array('required' => true, 'max_length' => 102)),
                    'estimate_time' => new sfValidatorNumber(array('required' => true)),
                    
                    //kartik for add field in activity        
                    'diuId' => new sfValidatorNumber(array('required' => false)),
                    'diuId' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($dius))),
                    'activity_code' => new sfValidatorString(array('required' => false)), 
                        
		    
		));

		$this->widgetSchema->setNameFormat('addProjectActivity[%s]');

	}
	
	public function save(){      
            
//            echo "<pre>";print_r($_POST);exit;
//		$projectId = $this->getValue('projectId');
                $projectId = $_POST['addProjectActivity']['projectId'];
//                jugni have made changes 20130625
//		$activityId = $this->getValue('activityId');
		$activityId = $_POST['addProjectActivity']['activityId'];
//		$estimate_time2=$this->getValue('estimate_time');
                $estimate_time2=$_POST['addProjectActivity']['estimate_time'];

//kartik for add field in activity                                 
                $diuId=$_POST['addProjectActivity']['diuId'];
                if(empty($diuId))
			{
				$diuId=0;
			}
                if(empty($activitycode))
			{
				$activitycode=0;
			}                        
                        
                 $activitycode = $_POST['addProjectActivity']['activity_code'];                        
//                echo $activitycode;
//                echo "<pre>";print_r($_POST);echo "</pre>";exit;
//                echo"========>".$projectId;
//                echo"========>".$estimate_time2;
//exit;
                
//kartik for add field in activity                                               
//                echo $diuId;exit;
		if(!empty ($activityId)){
			$activity = $this->getProjectService()->getProjectActivityById($activityId);
			$this->edited = true;
		} else {
			$activity = new ProjectActivity();
		}
		
		$activity->setProjectId($projectId);
                
                
                
//		$activity->setName($this->getValue('activityName'));   
//kartik for add field in activity 
                $activity->setName($_POST['addProjectActivity']['activityName']);
//kartik for add field in activity                 
                $activity->setestimate_time($estimate_time2);
                
                
                
//kartik for add field in activity                 
                $activity->setdiu_id($diuId);
                $activity->setactivity_code($activitycode);
                
//kartik for add field in activity 
                
		$activity->setIsDeleted(ProjectActivity::ACTIVE_PROJECT);
                
//                echo"<pre>";print_r($activitycode);echo"</pre>";
//                exit;
		$activity->save();
//                $tepo=$projectId.','.$estimate_time;
		return $projectId;
	}
    
    
//kartik for add field in activity     
       private function _getDius($diuId) {

        $diuService = new ProjectService();        
        $diuList = $diuService->getDiuList("", "");       
        $choices = array('' => '-- ' . __('Select') . ' --');                
        foreach ($diuList as $diu) {                        
            if (($diu->getIsDeleted() == ProjectDiu::ACTIVE_PROJECT_DIU) || ($diu->getDiuId() == $diuId)) {              
                $name = ($diu->getIsDeleted() == ProjectDiu::DELETED_PROJECT_DIU) ? $diu->getDiuId() . " (".__("Deleted").")" : $diu->getName();                
                $choices[$diu->getDiuId()] = $name;
            }
        }
        return $choices;
    }
//kartik for add field in activity     
    
    
        

}

?>