
/*
		* pop_up() takes url as argument and called from selectEmployee.php file.
		* pop_up() returns new window.
*/
function pop_up(url)
{
	newwin=window.open(url,"mywindow","resizable=yes,status=no,toolbar=no,width=500,height=270,left=350,top=30");
	newwin.focus();
}
