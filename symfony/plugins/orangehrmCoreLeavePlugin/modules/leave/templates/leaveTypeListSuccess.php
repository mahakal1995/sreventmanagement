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
?>

<?php
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.draggable.js');
use_javascript('../../../scripts/jquery/ui/ui.resizable.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');

use_stylesheet('../orangehrmCoreLeavePlugin/css/leaveTypeListSuccess');
use_javascript('../orangehrmCoreLeavePlugin/js/leaveTypeListSuccess');

?>
<div id="messagebar" class="messageBalloon_<?php echo $messageType; ?>" >
    <span>
<?php 
    if (!empty($messageType)) {
       echo $message; 
    }
?>
    </span>
</div>

<div id="mainDiv"> 
    <?php include_component('core', 'ohrmList'); ?>	
</div> 

<div id="deleteConfirmation" title="<?php echo __('Confirmation Required'); ?>" style="display: none;">
    <?php echo __(CommonMessages::DELETE_CONFIRMATION); ?>
    <div class="dialogButtons">
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>

<script type="text/javascript">
    //<![CDATA[
    var defineLeaveTypeUrl = '<?php echo url_for('leave/defineLeaveType'); ?>';    
    var lang_SelectLeaveTypeToDelete = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';  
    //]]>
</script>
