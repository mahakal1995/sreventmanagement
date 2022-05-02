<?php

class ProjectHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		$header3 = new ListHeader();
                $header4 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Customer Name',
		    'width' => '30%',
		    'isSortable' => true,
		    'sortField' => 'customerName',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'customerName'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'Project',
		    'width' => '15%',
		    'isSortable' => true,
		    'sortField' => 'projectName',
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'projectName',
			'placeholderGetters' => array('id' => 'projectId'),
			'urlPattern' => 'saveProject?projectId={id}'),
		));
                
        $header3->populateFromArray(array(
		    'name' => 'Project Description',
		    'width' => '30%',
		    'isSortable' => true,
		    'sortField' => 'description',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'description'),
		));

		$header4->populateFromArray(array(
		    'name' => 'Project Admins',
		    'width' => '25%',
			'isSortable' => true,
			'sortField' => 'projectAdminName',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'projectAdminName'),
		));

               
                
		$this->headers = array($header1,$header2,$header3,$header4);
	}
	
	public function getClassName() {
		return 'Project';
	}

}

?>
