 <!--  kartik new created for diu -->
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.validate.js')?>"></script>
<div class="formpage">
        <div class="outerbox">
        	<div class="top">
        		<div class="left"></div>
        		<div class="right"></div>
        		<div class="middle"></div>
        	</div>
        	<div class="maincontent">
            	<div class="mainHeading"><h2><?php echo __("Project DUs")?></h2></div>
					
			        	<form action="" method="post" name="frmDiu" id="frmDiu">
			            <label for="cmbProjectId"><?php echo __("Project")?></label>
			            <select class="formSelect"  name="id" id="id">
			            	<option value=""><?php echo __("Select Project")?></option>
							<?php foreach( $listProject as $project){?>
								<option value="<?php echo $project->getProjectId()?>" <?php if($currentProject==$project->getProjectId()){ echo "selected";}?>><?php echo $project->getName().'-'.$project->getCustomer()->getName()?></option>
							<?php }?>
						</select>
						</form>
			            <br class="clear"/>
			            <hr style="margin: 15px 0px; width: 420px; float: left;"/>
			            <?php if($hasProjectDiu){?>
				           <form action="<?php echo url_for("admin/deleteProjectDiu")?>" method="post" name="frmRemoveProjectAdmin" id="frmRemoveProjectAdmin">
		     					<input type="hidden" name="id" id="id" value="<?php echo  $currentProject?>"></input>
				            <div style="float: left;">
							<table width="250" class="simpleList">
								<thead>
									<tr>
									<th class="listViewThS1">
										<input type="checkbox"  value="" name="allCheck" class="checkbox" id="allCheck"/>
									</th>
									<th class="listViewThS1"><?php echo __("DU")?></th>
									</tr>
					    		</thead>
									    		<tbody>
									    		<?php foreach( $projectDiuList as $projectDiu){?>
									    		<tr>
									       			<td class="odd">
									       				<input type="checkbox"  value="<?php echo $projectDiu->getDiuId()?>" name="chkLocID[]" class="checkbox innercheckbox"/>
									       			</td>
											 			<td class="odd">
											 			<?php echo $projectDiu->getName()?>			 		
											 		</td>
												</tr>
							 		    		<?php }?>
							 		 		</tbody></table>
							</div>
							</form>
							 <br class="clear"/>
						<?php }else{?>
						 <br class="clear"/>
			           
			      			<div class="notice"><?php echo __("No Activities defined.")?></div>
				  
			           
			            <?php } ?>
			             <div class="formbuttons">
			                
			                <input type="button" value="<?php echo __("Add")?>"  tabindex="4"  id="addBtn" class="savebutton"/>
			                 <?php if($hasProjectDiu){?>
			                 <input type="button" value="Delete" tabindex="7"  id="deleteBtn" class="delbutton"/>
			                 <?php }?>                 
			            </div>
			            <br class="clear"/>
			                        
						<div style="display: none;" id="addDiuLayer">
							<form action="<?php echo url_for('admin/saveProjectDiu')?>" method="post" name="frmAddDiu" id="frmAddDiu">
						    	<input type="hidden" name="id" id="id" value="<?php echo  $currentProject?>"></input>
						    	<label for="diuName"><?php echo __("DU")?><span class="required">*</span></label>
					            <input type="text" class="formInputText" value="" id="diuName" name="diuName"/>
				                <br class="clear"/>
				                                
				                 <div class="formbuttons">
				                    
				                    <input type="button" value="<?php echo __("Save")?>"  tabindex="7"  id="addProjectDiu" class="savebutton"/>
				                    <input type="button" value="<?php echo __("Cancel")?>"  id="adminCancelBtn" tabindex="8"  class="clearbutton"/>
				                             
				                </div>                
			                  </form>   
						</div>
			            <br class="clear"/>            
			     
    		</div>
    		<div class="bottom">
    			<div class="left"></div>
        		<div class="right"></div>
        		<div class="middle"></div>
    		</div>
</div>
 <script type="text/javascript">

	$(document).ready(function() {

		//When click Add Button
		$("#addBtn").click(function() {
			$("#addDiuLayer").show();
		});

		//When click Add Button
		$("#adminCancelBtn").click(function() {
			$("#addDiuLayer").hide();
		});

		//When Change the project
		$("#id").change(function() {
			$("#frmDiu").submit();
			
		});

		
		//When Adding project diu
		$("#addProjectDiu").click(function() {
			$("#frmAddDiu").submit();
			
		});

		//When delete project diu
		$("#deleteBtn").click(function() {
			$("#frmRemoveProjectAdmin").submit();
			
		});

		//Validate the form
		 $("#frmAddDiu").validate({
			
			 rules: {
			 	diuName: { required: true }
			 	
		 	 },
		 	 messages: {
		 		diuName: '<?php echo __(ValidationMessages::REQUIRED); ?>'
		 		
		 	 }
		 });

			// When Click Main Tick box
			$("#allCheck").change(function() {
				if ($('#allCheck').attr('checked')) {
					$('.innercheckbox').attr('checked','checked');
				}else{
					$('.innercheckbox').removeAttr('checked');
				}
				
			});
	 });
</script> 