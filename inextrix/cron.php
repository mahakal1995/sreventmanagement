#!/usr/bin/php -q
<?php
include('include/config/config.php');
include('include/process.php');

	if($argv[1] == 'timesheetFridayReminder')
	{
		 timesheetFridayReminder();
	}
	if($argv[1] == 'timesheetMondayReminder')
	{
		 timesheetMondayReminder();
	}
?>
