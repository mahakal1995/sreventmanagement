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

class SubunitForm extends ohrmFormComponent {

    public function configure() {
        $properties = new ohrmFormComponentProperty();

        $properties->setService(new CompanyStructureService());
        $properties->setMethod('getSubunitById');
        $properties->setParameters(array(1));
        
//Name:sangani jagruti
//Date:2014-03-21
//Purpose:Change the name of Unit Id to groupcode
    
        $properties->setFields(array(
            'Id' => 'getId',
            'Unit Id' => 'getUnitId',
            'Name' => 'getName',
            'Description' => 'getDescription',
            'Parent' => ''
        ));

        $properties->setIdField('Id');

        $properties->setFormStyle('width: auto; max-width: 600px;');

        $properties->setFieldTypes(array(
            'Parent' => 'hidden',            
            'Id' => 'hidden',     
            'Description' => 'textarea'
        ));

        $properties->setRequiredFields(array('Name'));

        $this->setPropertyObject($properties);

        $this->hasFormNavigatorBar(false);
    }

}