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

class WorkWeekForm extends sfForm {

    private $workWeekService;
    private $workWeekEntity;

    /**
     * Sets WorkWeekService
     * @param WorkWeekService $service
     */
    public function setWorkWeekService(WorkWeekService $service) {
        $this->workWeekService = $service;
    }

    /**
     * Getter for WorkWeekService
     * @return WorkWeekService
     */
    public function getWorkWeekService() {
        if (!($this->workWeekService instanceof WorkWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }

    /**
     * Getter method for WorkWeek Entity
     * @return WorkWeek
     */
    public function getWorkWeekEntity() {
        if (!($this->workWeekEntity instanceof WorkWeek)) {
            $this->workWeekEntity = new WorkWeek();
        }
        return $this->workWeekEntity;
    }

    /**
     * Sets the WorkWeek Entity
     * @param WorkWeek $workWeek
     */
    public function setWorkWeekEntity(WorkWeek $workWeek) {
        $this->workWeekEntity = $workWeek;
    }

    /**
     * Configuring WorkWeek form widget
     */
    public function configure() {

        $this->setWorkWeekEntity($this->getDefault('workWeekEntity'));

        $this->setValidators($this->getDayLengthValidators());
        $this->setWidgets($this->getDayLengthWidgets());
        $this->setDefaults($this->getDayLengthDefaults());

        $this->widgetSchema->setLabels($this->getDayLengthLabels());
        $this->widgetSchema->setNameFormat('WorkWeek[%s]');

        $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'validateWorkWeekValue'))));
        
        $this->getWidgetSchema()->setFormFormatterName('BreakTags');
    }

    /**
     * Read WorkWeek Objects
     * @param string $data
     * @return array Array of WorkWeek objects
     */
    public function getWorkWeekObjects($data) {

        $daysList = WorkWeek::getDaysList();
        
        $workWeekList = array();

        foreach ($data as $day => $length) {
            $fday = substr($day, -1); // strip "select_" in the day param
            if (array_key_exists($fday, $daysList)) {
                $workWeek = $this->getWorkWeekService()->readWorkWeek($fday);
                $workWeek->setLength($length);
                $workWeekList[] = $workWeek; // this will return only allowed work week objects
            } else {
                throw new LeaveServiceException("Invaid Day");
            }
        }
        return $workWeekList;
    }
    
    
    
    
    
    
     
    
    

    /**
     * Validate WorkWeek form elements passed by the view // prevent form element alteration
     *
     * @param sfValidator $validator
     * @param array $values
     * @return array $values Array of Values
     */
    public function validateWorkWeekValue($validator, $values) {

        $daysList = WorkWeek::getDaysList();
        $workWeekList = array();

        foreach ($values as $day => $length) {

            if (preg_match('/day_length_(Mon|Tues|Wednes|Thurs|Fri|Sat|Sun)day$/', $day)) {
                $dayTerm = str_replace('day_length_', '', $day);

                if (!in_array($dayTerm, $daysList)) {
                    $error = new sfValidatorError($validator, 'Invalid WorkWeek!');
                    throw new sfValidatorErrorSchema($validator, array($day => $error));
                }
            }
        }
        
        return $values;
    }

    /**
     *
     * @return sfWidgetFormSelect[]
     */
    protected final function getDayLengthWidgets() {
        $dayLengths = WorkWeek::getDaysLengthList();
        
        
    /**        
        By:kartik gondalia
        Date:23-03-2013
        Purpose:add this variable for add option for saturday as a 2nd and 4th saturday 
        Keep Original line here if:-
    */
        $sat_dayLengths = WorkWeek::get_Sat_DaysLengthList();
    /*====================================================================*/
        
        
        


        /* Making compatible with i18n */
        foreach ($dayLengths as $dayLength => $dayLengthTerm) {
            $dayLengths[$dayLength] = __($dayLengthTerm);
        }
        
        $formWidgets = array();
        $daysOfWeek = WorkWeek::getDaysList();
        
        
         
        

        foreach ($daysOfWeek as $day) {        
    /**        
        By:kartik gondalia
        Date:23-03-2013
        Purpose:add this variable for add option for saturday as a 2nd and 4th saturday 
        Keep Original line here if:-
    */             
            if($day=="Saturday")
            {
                $formWidgets['day_length_' . $day] = new sfWidgetFormSelect(
                    array('choices' => $sat_dayLengths),
                    array('class' => 'formSelect')
            );
            }
/*====================================================================================*/
            else
            {
                 $formWidgets['day_length_' . $day] = new sfWidgetFormSelect(
                    array('choices' => $dayLengths),
                    array('class' => 'formSelect')
            );
            }
           
        }

        return $formWidgets;
    }

    protected final function getDayLengthLabels() {
        $formLabels = array();
        $daysOfWeek = WorkWeek::getDaysList();

        foreach ($daysOfWeek as $day) {
            $formLabels['day_length_' . $day] = __($day);
        }

        return $formLabels;
    }
    
    protected final function getDayLengthValidators() {
        $formValidators = array();
        
        $daysOfWeek = WorkWeek::getDaysList();
        $choices = array_keys(WorkWeek::getDaysLengthList());
        
            /**        
                By:kartik gondalia
                Date:23-03-2013
                Purpose:add this variable for add option for saturday as a 2nd and 4th saturday 
                Keep Original line here if:-
            */
          $choices1 = array_keys(WorkWeek::get_Sat_DaysLengthList());
        /*==========================================================================*/
        
        foreach ($daysOfWeek as $day) {

            
    /**        
        By:kartik gondalia
        Date:23-03-2013
        Purpose:add this variable for add option for saturday as a 2nd and 4th saturday 
        Keep Original line here if:-
    */   
            if($day=="Saturday")
            {
                
                    $validator = new sfValidatorChoice(
                                array(
                                    'choices' => $choices1,
                                ),
                                array(
                                    'invalid' => 'Invalid work week for ' . $day,
                                    'required' => 'Value for ' . $day . ' is required',
                                )
                );
                
            }
      /*=======================================================================*/
            else
            {
            
                    $validator = new sfValidatorChoice(
                                    array(
                                        'choices' => $choices,
                                    ),
                                    array(
                                        'invalid' => 'Invalid work week for ' . $day,
                                        'required' => 'Value for ' . $day . ' is required',
                                    )
                    );
            }
            $formValidators['day_length_' . $day] = $validator;
        }
        
        return $formValidators;
    }
    
    protected final function getDayLengthDefaults() {
        $formDefaults = array();
        $daysOfWeek = WorkWeek::getDaysList();

        foreach ($daysOfWeek as $isoValue => $day) {
            $formDefaults['day_length_' . $day] = $this->getWorkWeekEntity()->getLength($isoValue);
        }

        return $formDefaults;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = '/orangehrmCoreLeavePlugin/js/defineWorkWeekSuccess.js';
        $javaScripts[] = '/orangehrmCoreLeavePlugin/js/defineWorkWeekSuccessValidate.js';

        return $javaScripts;
    }    

}
