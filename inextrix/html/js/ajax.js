//**--Timesheet popup form name change--**//
// Change: Changed form name from frmEmp to frmTSpopup.
//**---------------------------------------------------------------------------------------------------------------**//

var xmlHttp
/*****************************************************************************************************/
var xmlHttp1

function tsGetValue(strEmpId)
{
    xmlHttp1=GetXmlHttpObject()
    if (xmlHttp1==null)
    {
        alert ("Browser does not support HTTP Request")
        return
    }
    if(strEmpId !='x')
    {
        var url="index.php?action=tsFillStartdate"+"&empid="+strEmpId;
        xmlHttp1.onreadystatechange=stateChanged1
        xmlHttp1.open("GET",url,true)
        xmlHttp1.send(null)
    }
    else
    {
        removeOptions(document.frmTSpopup.lb);
    }
}

function stateChanged1()
{
    if (xmlHttp1.readyState==4 || xmlHttp1.readyState=="complete")
    {
        removeOptions(document.frmTSpopup.lb);
        total = xmlHttp1.responseText;

        values = total.split(';');

            var j="";
            for(i=0;i<values.length;i++)
            {
                comavalue=values[i].indexOf(',')
                if(comavalue != -1)
                {
                    var optn = document.createElement("OPTION");
                    document.frmTSpopup.lb.options.add(optn);
                    id_date=values[i].split(',');
                    split_time=id_date[1];
                    start_date=split_time.split(' ');
                    optn.text = start_date[0];
                    optn.value = id_date[0];
                }
            }
    }
}
/*******************************************************************************************************************************/
/*
        * GetValue() takes Empid and TimesheetStatus values as arguments.
        * Onchange of  CmbEmpName and CmbTimesheetStatus comboboxes using ajax it returns timsheet_id and Start_date value to selectEmployee.php file.
*/




function GetValue(strEmpId,strtimesheetstatus)
{
    

xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 }
if(strEmpId !='x' && strtimesheetstatus!='x')
{
    

var url="index.php?action=AjaxTimesheet"+"&empid="+strEmpId+"&timesheetstatus="+strtimesheetstatus;
xmlHttp.onreadystatechange=stateChanged 
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
}
//**--Filling Timesheets on employee and status selecion--**//
// Change: added call to function removeOptions.
// Purpose: to remove all timesheets from list if status or employee is not selected
//**---------------------------------------------------------------------------------------------------------------**//
else
{
removeOptions(document.frmTSpopup.lb);
}
//**---------------------------------------------------------------------------------------------------------------**//
}

/*
		* removeOptions() takes listbox name as argument.
		* It clears previously filled values from listbox.
*/
function removeOptions(selectbox)
{	
	var i;
	for(i=selectbox.options.length-1;i>=0;i--)
	{
		selectbox.remove(i);
	}
}
/*
		* stateChanged() called from GetValue() when xmlHttp protocols state changes.
		* when xmlHttp protocols state changes to 4 or complete then we can get output from our process.php 
*/
function stateChanged()
{
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 {
	removeOptions(document.frmTSpopup.lb);
	total = xmlHttp.responseText;
	values = total.split(';');

		//document.getElementById('DivEmpName').innerHTML='No Timesheets Available..';

        var j="";
		for(i=0;i<values.length;i++)
		{

			comavalue=values[i].indexOf(',')
			if(comavalue != -1)
			{
				var optn = document.createElement("OPTION");
				document.frmTSpopup.lb.options.add(optn);
				id_date=values[i].split(',');

				split_time=id_date[1];
				start_date=split_time.split(' ');
				optn.text = start_date[0];
                optn.value = id_date[0];

			}
		}
 }
}

//**--Employee selecion based on employee status--**//
// Change: added two new functions GetEmpValue() and stateChangedemp()
// Purpose: To use Ajax to filter employee list depending on employee status selected. Both the functions work same as GetValue() and stateChanged()
//**---------------------------------------------------------------------------------------------------------------**//
/*
		* GetEmpValue() takes Employee Status Code as argument
		* Onchange of  CmbEmpStatus comboboxe using ajax it returns employee number and details to employee selection box. 
*/
function GetEmpValue(strEmpStat)
{
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 }
var url="index.php?action=AjaxEmployee"+"&statcd="+strEmpStat;
xmlHttp.onreadystatechange=stateChangedemp
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
}




/*
		* stateChangedemp() called from GetEmpValue() when xmlHttp protocols state changes.
		* when xmlHttp protocols state changes to 4 or complete then we can get output from our process.php 
*/
function stateChangedemp() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 {

	removeOptions(document.frmTSpopup.txtRepEmpID);
	total = xmlHttp.responseText;
	values = total.split(';');
	
		//document.getElementById('DivEmpName').innerHTML='No Timesheets Available..';
	var optn_default = document.createElement("OPTION");
	document.frmTSpopup.txtRepEmpID.options.add(optn_default);
	optn_default.text =  "---Select Employee---";
	optn_default.value ="x";
		for(i=0;i<values.length;i++)
		{
			comavalue=values[i].indexOf(',')
			if(comavalue != -1)
			{
				var optn = document.createElement("OPTION");
				document.frmTSpopup.txtRepEmpID.options.add(optn);
				id_date=values[i].split(',');
				optn.text =  id_date[1];
				optn.value = id_date[0];
			}
		}
	

 } 
}
//**---------------------------------------------------------------------------------------------------------------**//

/*
		* GetXmlHttpObject() called from GetValue() when xmlHttpobject we need to create.
		* GetXmlHttpObject() returns xmlHttpobject after checking our browse's need.
*/
function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}