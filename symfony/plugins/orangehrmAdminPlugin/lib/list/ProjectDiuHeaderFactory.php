<?php
// kartik new created for diu   
class ProjectDiuHeaderFactory extends ohrmListConfigurationFactory {
	
	protected function init() {

		$header1 = new ListHeader();
                
		$header1->populateFromArray(array(
		    'name' => 'DU Name',
		    'width' => '100%',
		    'elementType' => 'link',
		     'elementProperty' => array(
			'labelGetter' => 'getName',
			'urlPattern' => 'javascript:'),
		));
		$this->headers = array($header1);
	}

	public function getClassName() {
		return 'ProjectDiu';
	}
}
 