
<?php $i = 0; ?>
<option value="-1">
    <?php echo '-- ' . __("Select") . ' --'; ?>
</option>
<?php

//Name:sangani jagruti
//Date:2014-04-17
//purpose: add activity code in activity name dropdown.    

foreach ($activityList as $activity): ?>
        <option value="<?php echo $activity->getActivityId(); ?>">
    <?php 
        echo $activity->getactivity_code()." - ".$activity->getName();
//        echo $activity->getName();
        $i++; ?>
    </option>
<?php endforeach; ?>
