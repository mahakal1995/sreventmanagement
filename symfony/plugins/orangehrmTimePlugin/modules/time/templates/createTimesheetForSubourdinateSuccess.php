<?php /**
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
 */ ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<?php echo stylesheet_tag('../orangehrmTimePlugin/css/createTimesheetForSubourdinateSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/createTimesheetForSubourdinateSuccess'); ?>
<!-- jugni have made changes 20130621 -->
<?php $month=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nev','Dec');?>
<div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>

 &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;<?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_CREATE, $sf_data->getRaw('allowedToCreateTimesheets'))): ?>
    <input type="button" class="addTimesheetbutton" name="button" id="btnAddTimesheet"
           onmouseover="moverButton(this);" onmouseout="moutButton(this);"
           value="<?php echo __('Add Timesheet') ?>" />
       <?php endif; ?>

<div id="createTimesheet">
    <br class="clear"/>
    <form  id="createTimesheetForm" action=""  method="post">
    
<!--     jugni have made changes 20130621 ======================================================================================================= -->
        <?//php echo $createTimesheetForm['_csrf_token']; ?>
        <?//php echo $createTimesheetForm['date']->renderLabel(__('Select a Day to Create Timesheet')); ?>
        <?//php echo $createTimesheetForm['date']->render(); ?>
<!--         <input id="DateBtn" type="button" name="" value="" class="calendarBtn" /> -->
        <?//php echo $createTimesheetForm['date']->renderError() ?>
        
        
         <?php echo '&nbsp &nbsp &nbsp &nbsp &nbsp Select Month and Year to create Timesheet: &nbsp ';?>
              <select id="drpmonth" name="drpmonth">
            <?php 

            foreach($month as $key=>$mon)
            {
            if($mon==date("M"))
            {
            ?>
            <option value="<?php echo $key+1;?>" selected="selected"><?php echo $mon;?></option>
            <?php } else{?>
            <option value="<?php echo $key+1;?>"><?php echo $mon;?></option>
            <?}}?>
            </select>
            <?php echo '&nbsp';?>
            <select id="drpyear" name="drpyear">
            <?php for($i=2012;$i<= date("Y")+1;$i++)
            {
            if($i==date("Y"))
	    {
	      ?>
	      <option value="<?php echo $i;?>" selected="selected"><?php echo $i;?></option>
	      <?php
	    }
	    else
	    {
            ?>
            
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
            <?}}?>
            </select>
            <?php echo '&nbsp';?>
            <input type="button" class="submitbutton" name="btnCreateTimesheet" id="btnCreateTimesheet"
                               onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                               value="<?php echo __('Submit') ?>" />
<!-- ================================================================================================================================                               -->
        <br class="clear"/>
    </form>
    <?php
//jugni have made changes 20130621
 $secret =md5(php_uname());
 ?>
<form action="/fcs/symfony/web/index.php/time/viewEmployeeTimesheet" id="employeeSelectForm" method="post" >
                            <input class="inputFormatHint" id="employee" type="hidden" name="time[employeeName]" value="Type for hints..." />
                            <input type="hidden" name="time[employeeId]" value="2" id="time_employeeId" />
                            <input type="hidden" name="time[_csrf_token]" value="<?=$secret?>" id="time__csrf_token" />	
                            <input type="hidden" name="start_date" value="" id="start_date"/> 
                            <input type="hidden" name="end_date" vallue="" id="end_date"/>
</form>
<!-- ======================================================================================================================== -->
</div>


<script type="text/javascript">
                                                    
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var employeeId = "<?php echo $employeeId; ?>";
    var linkForViewTimesheet="<?php echo url_for('time/viewTimesheet') ?>";
    var validateStartDate="<?php echo url_for('time/validateStartDate'); ?>";
    var createTimesheet="<?php echo url_for('time/createTimesheet'); ?>";
    var returnEndDate="<?php echo url_for('time/returnEndDate'); ?>";
    var currentDate= "<?php echo $currentDate; ?>";
    var lang_noFutureTimesheets= "<?php echo __("Failed to Create: Future Timesheets Not Allowed"); ?>";
    var lang_overlappingTimesheets= "<?php echo __("Timesheet Overlaps with Existing Timesheets"); ?>";
    var lang_timesheetExists= "<?php echo __("Timesheet Already Exists"); ?>";
    var lang_invalidDate= '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
//     jugni have made changes 20130621
   var linkToCreateSlot="<?php echo url_for('time/createSlot') ?>";
</script>
