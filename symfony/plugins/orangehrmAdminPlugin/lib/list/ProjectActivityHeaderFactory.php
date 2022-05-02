<?php

class ProjectActivityHeaderFactory extends ohrmListConfigurationFactory {
	
	protected function init() {

		$header1 = new ListHeader();
                $header2 = new ListHeader();
                $header3 = new ListHeader();

		$header1->populateFromArray(array(
		    'name' => 'Activity Name',
		    'width' => '40%',
		    'elementType' => 'link',
		     'elementProperty' => array(
			'labelGetter' => 'getName',
			'urlPattern' => 'javascript:'),
		));
//                jaydeep
                
                $header3->populateFromArray(array(
		    'name' => 'Estimated hours',
		    'width' => '20%',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getestimate_time'),
		));
                
                 $header2->populateFromArray(array(
		    'name' => 'Activity Code',
		    'width' => '40%',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getactivity_code'),
		));
                

		$this->headers = array($header1,$header2,$header3);
	}

	public function getClassName() {
		return 'ProjectActivity';
	}
}
