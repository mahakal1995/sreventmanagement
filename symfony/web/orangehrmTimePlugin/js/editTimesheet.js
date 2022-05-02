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
var classStr;
var activityId;
var date;
var cmnt;
$(document).ready(function() {
    var currentId;
    var weekday=new Array(7);
    weekday[1]="Monday";
    weekday[2]="Tuesday";
    weekday[3]="Wednesday";
    weekday[4]="Thursday";
    weekday[5]="Friday";
    weekday[6]="Saturday";
    weekday[0]="Sunday";


    $(".project").click(function(){
        element = $(this)
        if($(element).val() == typeForHints){
            this.value = "";
            $(this).removeClass("inputFormatHint");
        }

    });

    $(".project").focus(function(){
        element = $(this);

        if (element.data('init') != true) {
            initAutoComplete(element);
            element.data('init', true);
        }

    });

    $(".deletedRow").attr("disabled", "disabled");

    $(".project").each(function(){
        element = $(this)
        if($(element).val() == typeForHints){
            $(element).addClass("inputFormatHint");
        }
	$(element).val($(element).val().replace("##", ""));
    });

    //Auto complete
    function initAutoComplete(element) {
        element.autocomplete(projectsForAutoComplete, {

            formatItem: function(item) {
                var temp = $("<div/>").html(item.name).text();
                return temp.replace("##", "");
            }
            ,
            matchContains:true
        }).result(function(event, item) {

            currentId = $(this).attr('id');

            var temparray = currentId.split('_');
            var temp = '#'+temparray[0]+'_'+temparray[1]+'_'+'projectActivityName';
            var decodedfullName = $("<div/>").html(item.name).text();

            var array = decodedfullName.split(' - ##');

            var r = $.ajax({
                type: 'POST',
                url: getActivitiesLink,
                data: {
// 		  Rushika Changes = Swapp customerName and ProjectName
//                     customerName: array[0],
//                     projectName: array[1]
			projectName: array[0],
			customerName: array[1]
                },

                success: function(msg){
                    $(temp).html(msg);
                    var flag = validateProject();
                    if(!flag) {
                        $('#btnSave').attr('disabled', 'disabled');
                        $('#validationMsg').attr('class', "messageBalloon_failure");
                    }
                    else{
                        $('#btnSave').removeAttr('disabled');
                        $('#validationMsg').removeAttr('class');
                        $('#validationMsg').html("");
                        $(".messageBalloon_success").remove();
                    }
                }
            }).responseText;
        }
        );
    }

    $("#commentDialog").dialog({
        autoOpen: false,
        width: 350,
        height: 235
    });

    $("#commentCancel").click(function() {
        $("#commentDialog").dialog('close');
    });

    $("#commentSave").click(function() {

        if(validateTimehseetItemComment()){

            $("#commentError").html("");
            var comment = $("#timeComment").val();


            saveComment(timesheetId, activityId, date, comment, employeeId);
            $("#commentDialog").dialog('close');
        }

    });

    $(".plainbtn").click(function(e){
        $(this).addClass("e-clicked");
    });

    $('#timesheetForm').submit(function(){
        $('#validationMsg').removeAttr('class');
        if( $(this).find(".e-clicked").attr("id") == "submitSave" ){
            var projectFlag = validateProject();
            if(!projectFlag) {
                $('#btnSave').attr('disabled', 'disabled');
                $('#validationMsg').attr('class', "messageBalloon_failure");
                return false;
            }
            var inputFlag = validateInput();
            if(!inputFlag){
                $('#validationMsg').attr('class', "messageBalloon_failure");
                return false;
            }
            var rowFlag = validateRow();
            if(!rowFlag){
                $('#validationMsg').attr('class', "messageBalloon_failure");
                return false;
            }
        }
        if( $(this).find(".e-clicked").attr("id") == "submitRemoveRows" ){
            var deleteFlag = false;
            $('.toDelete').each(function(){
                element = $(this);
                if($(element).is(':checked')){
                    deleteFlag =  true;
                }
            });
            if(!deleteFlag){
                $('#validationMsg').html(select_a_row);
                $('#validationMsg').attr('class', "messageBalloon_failure");
            }
            return deleteFlag;
        }
        $( this ).find("input[type=\"submit\"]").removeClass("e-clicked");

    });
    function validateInput() {
       var days=$('#allholidays').val();
//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day
       var work_day=$('#allworkdays').val();
       var allworkday=work_day.split(',');
// ************************************************************************
       var alldays=days.split(',');


        var flag = true;
        $(".messageBalloon_success").remove();
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");
        var errorStyle = "background-color:#FFDFDF;";
    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:variables for validation
        Keep Original line here if:-
    */
        var work_station = $('#work_station').val();
        var emp_work_hours = $('#emp_work_hours').val();
//       var holiday = new Array();
        holiday = $('#holiday').val();

//         var emp_work = $('.ot_worker').val();
//         var emp_wor = $('.ot').val();




        holiday=JSON.parse(holiday);

//        holiday= "'"+holiday+"'";

//alert(holiday);

//var date = holiday;
//var elem = date.split(',');
//day = elem[0];
//month = elem[1];
//year = elem[2];
//
// alert(day);
//  alert(month);
//   alert(year);


//        var col_field = [];
//        var col_arr = "";
//        col_arr =holiday;
//        for (var key in col_arr) {
//            col_field.push(col_arr[key]);
//        }
















        var counter = 0;
        var prev_val;
/*=================================================================*/

        $('.items').each(function(){
            element = $(this);
            $(element).removeAttr('style');




        /**
            By:kartik gondalia
            Date:23-03-2013
            Purpose:validation
            Keep Original line here if:-
        */
            if(work_station=="NULL")
            {
//                alert("ooooooooooo");

                 if(parseFloat($(element).val()) > emp_work_hours) {
                    $('#validationMsg').html(st_Time_check);

                    var errorStyle = "background-color:#FFDFDF;";
                    $(element).attr('style', errorStyle);
                    flag = false;
                }
                  if((!(/^[0-9]+\.?[0-9]?[0-9]?$/).test($(element).val())) && $(element).val() != "") {
                    var temp = $(element).val().split(":");
                    if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[0]) || !(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[1])){
                        $('#validationMsg').html(lang_not_numeric);
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }else if(temp[0]>23 || temp[1]>59){
                        $('#validationMsg').html(lang_not_numeric);
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }

                }


                  if(flag){
                    id=element.attr('id');
                    idArray= id.split("_");
                    var errorStyle = "background-color:#FFDFDF;";
                    var flag1=  validateVerticalTotal_worker(idArray[2]);
                    if(!flag1){
//                        alert("kakakakak");
                        $('#validationMsg').html(st_Time_check);
                        $(element).attr('style', errorStyle);

                        flag=false;
                    }
                    else{
                        $(".messageBalloon_success").remove();
                        $('#validationMsg').removeAttr('class');
                    }
                }



            }
            else
            {
//                alert("11111111111");
                        counter++;
                        if((counter%2==0) && counter == 1){
                        prev_val = $(element).val();
//                        alert(prev_val);
//================================>  St time validation from here  =========================================>
                            if(parseFloat(prev_val) > emp_work_hours) {
//                                    alert("knknknknk");
                                    $('#validationMsg').html(st_Time_check);
                                    var errorStyle = "background-color:#FFDFDF;";
                                    $(element).attr('style', errorStyle);
                                    flag = false;
                                }

                        }
                        if((counter%2==0) && (counter!=1)){
//                            alert(parseFloat($(element).val()) +"============>"+parseFloat(prev_val));

                               var flag12=  validateVerticalTotal_chk_st(idArray[2]);
//                               alert(emp_work_hours+"===============>"+parseFloat(flag12));

                                 idArray= id.split("_");
                                var chk_holiday=currentWeekDates[idArray[2]];
//jugni change
                                  var test_holi=alldays.indexOf(chk_holiday);
//                                var test_holi=holiday.indexOf(chk_holiday);

//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day
                                  var work_day=allworkday.indexOf(chk_holiday);
                                 var dt=new Date(chk_holiday);
                                 var day=dt.getDay();

//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day

                                if(flag12 < emp_work_hours && test_holi < 0 && ((weekday[day]!="Sunday") || work_day==0))
                                    {
//                                        alert("set zero");
                                            $(element).attr('readonly', true);
                                            $(element).val("0:00");
                                    }
                                if(flag12 >= emp_work_hours)
                                    {
                                        $(element).attr('readonly', false);
                                    }


                            var temp_val = parseFloat(prev_val)+parseFloat($(element).val());
                            if(parseFloat(temp_val) > 24 ) {
                                    $('#validationMsg').html(st_ot_addition);
                                    var errorStyle = "background-color:#FFDFDF;";
                                    $(element).attr('style', errorStyle);
                                    flag = false;
                                }
                            counter=0;

                        }
             /*========================================================*/
                        if(flag){

                            id=element.attr('id');
                            idArray= id.split("_");
                            var errorStyle = "background-color:#FFDFDF;";
                            var flag1=  validateVerticalTotal(idArray[2]);
                            if(!flag1){
                                $('#validationMsg').html(incorrect_total);
                                $(element).attr('style', errorStyle);

                                flag=false;
                            }
                            else{
                                $(".messageBalloon_success").remove();
                                $('#validationMsg').removeAttr('class');
                            }
                        }




            if($(element).val()){
                if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test($(element).val())) {
                    var temp = $(element).val().split(":");

                    if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[0]) || !(/^[0-9]+\.?[0-9]?[0-9]?$/).test(temp[1])){
                        $('#validationMsg').html(lang_not_numeric);
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }else if(temp[0]>23 || temp[1]>59){
                        $('#validationMsg').html(lang_not_numeric);
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }

                }

                else  {
                    if(parseFloat($(element).val()) > 24) {
                        $('#validationMsg').html(lang_not_numeric);
                        var errorStyle = "background-color:#FFDFDF;";
                        $(element).attr('style', errorStyle);
                        flag = false;
                    }
                }



                if(flag){

                    id=element.attr('id');
                    idArray= id.split("_");
                    var errorStyle = "background-color:#FFDFDF;";
                    var flag1=  validateVerticalTotal(idArray[2]);
                    if(!flag1){
                        $('#validationMsg').html(incorrect_total);
                        $(element).attr('style', errorStyle);

                        flag=false;
                    }
                    else{
                        $(".messageBalloon_success").remove();
                        $('#validationMsg').removeAttr('class');
                    }
                }





    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:validation
        Keep Original line here if:-
    */
                if(flag){
                    idOt=element.attr('id');
                    idArrayOt= idOt.split("_");
                    var errorStyle = "background-color:#FFDFDF;";
                    var flag1=  validateVerticalTotalOt(idArrayOt[2]);
                    if(!flag1){
                        $('#validationMsg').html(incorrect_total);
                        $(element).attr('style', errorStyle);

                        flag=false;
                    }
                    else{
                        $(".messageBalloon_success").remove();
                        $('#validationMsg').removeAttr('class');
                    }
                }

                    if(flag){
                        id=element.attr('id');
                        idArray= id.split("_");
                        var errorStyle = "background-color:#FFDFDF;";
                        var flag1=  validateVerticalTotal_worker(idArray[2]);
                        if(!flag1){
//                            alert("hkhkhkhk");
                            $('#validationMsg').html(st_Time_check);
                            $(element).attr('style', errorStyle);

                            flag=false;
                        }
                        else{
                            $(".messageBalloon_success").remove();
                            $('#validationMsg').removeAttr('class');
                        }
                    }
/*===================================================================================*/
                }
            }
        });

        return flag;
    }

$('.items').change(function()
{
    var work_hours=$('#work_hours').val();
    var noOfDays=$('#noOfDays').val();
    var date=$('#alldates').val();
    var alldate=date.split(',');
    var days=$('#allholidays').val();
    var alldays=days.split(',');
    var flag = validateInput();

//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day
       var work_day=$('#allworkdays').val();
       var allworkday=work_day.split(',');
//   *******************************************************************
    if(!flag)
    {
        $('#btnSave').attr('disabled', 'disabled');
        $('#validationMsg').attr('class', "messageBalloon_failure");
    }
    else
    {
        var allval=0;
        var otallval=0;
        var Hours=new Array();
        var Minutes=new Array();
        for(var i=0;i<rows-1;i++)
        {
            for(var j=0;j<noOfDays;j++)
            {
                if(i==0)
                {
                    Hours[j]=0;
                    Minutes[j]=0;
                }

                if($("#initialRows_"+i+"_"+j).val()!="")
                    allval=$("#initialRows_"+i+"_"+j).val();
                else
                 allval="0:00";

                if($("#initialRows_ot_"+i+"_"+j).val()!="")
                {
                    otallval=$("#initialRows_ot_"+i+"_"+j).val();
                }
                else
                {
                    otallval="0:00";
                }
                if (typeof otallval === 'undefined')
                {
                    otallval="0:00";
                }

                //rushika 20130702
                var val=new Array();
                if(allval.match(":"))
                {
                    val=allval.split(':');

		    if(val[1].length==1)
		      val[1]=val[1]*10;
                   /* if(val[1] <=9 && val[1] >=0)
                    {
                     val[1]=val[1]*10;
                    }*/
                }
                else if(allval.match("."))
                {
                    val=allval.split('.');
                    if (typeof val[1] === 'undefined')
                    {
                      val[1]=0;
                    }
                    if(val[1].length==1)
		      val[1]=val[1]*10;
	 	/*
                    if(val[1] <=9 && val[1] >=0)
                    {
                     val[1]=val[1]*10;
                    }
                  */
                    val[1]=(val[1]*60)/100;
                }
                var val1=new Array();
                if(otallval.match(":"))
                {
//                    alert(":");s
                    val1=otallval.split(':');
		    if(val1[1].length==1)
		      val1[1]=val1[1]*10;
                   /* if(val1[1] <=9 && val1[1] >=0)
                    {
                     val1[1]=val1[1]*10;
                    }*/
                 }
                else if(otallval.match("."))
                {
                    val1=otallval.split('.');
                    if (typeof val1[1] === 'undefined')
                    {
                      val1[1]=0;
                    }
                    if(val1[1].length==1)
		      val1[1]=val1[1]*10;
/*
                    if(val1[1] <=9 && val1[1] >=0)
                    {
                     val1[1]=val1[1]*10;
                    }
  */                  val1[1]=(val1[1]*60)/100;
                }

                var tot=(val[0]*60)+parseInt(val[1]);
                Hours[j]=parseInt(Hours[j])+parseInt(val[0])+parseInt(val1[0]);
                Minutes[j]=parseInt(Minutes[j])+parseInt(val[1])+parseInt(val1[1]);
            }
        }
        for(var i=0;i<noOfDays;i++)
        {
            if(Minutes[i]>=60)
            {
                var extra=Math.floor(Minutes[i]/60);
                Hours[i]=parseInt(Hours[i])+parseInt(extra);
                Minutes[i]=Minutes[i]%60;
            }
            var data=alldate[i];
            var dt=new Date(data);
            var day=dt.getDay();
	     if(Minutes[i]<=9 && Minutes[i]>=0)
            {
                txtmin="0"+Minutes[i];
            }
            else
            {
                txtmin=Minutes[i];
            }
	    Minutes[i]=parseInt((Minutes[i]*100)/60);
	    if((Minutes[i]+"").length<=1)
	    {
	      Minutes[i] = "0"+Minutes[i];
	    //  alert(Minutes[i]);
	    }
// 	    alert(Minutes[i]);
            TotalTime=Hours[i]+"."+Minutes[i];
	    //___________________________________________________________________________________
	    //alert(TotalTime +" "+ work_hours +"RRRR".length+(Minutes[i]+"").length);

            if(isNaN(txtmin))
            {
                txtmin="00";
            }
//            alert(Hours[i]+":"+txtmin);

//                                Change by : Rushika Patel
//                                Date : 17-12-2013
//                                Change : make sunday as working day
            if(parseFloat(TotalTime) < parseFloat(work_hours) && (alldays.indexOf(data)== -1) && (weekday[day]!="Sunday" || (weekday[day]=="Sunday" && allworkday.indexOf(data)==0)))
            {
                $(".ColumnTotal"+i).html((Hours[i]+":"+txtmin).bold()).css("color" , "red");
            }
            else
            {
                $(".ColumnTotal"+i).html((Hours[i]+":"+txtmin).bold()).css("color" , "black");
            }
        }
            $('#btnSave').removeAttr('disabled');
    }
});

    function validateVerticalTotal(id){

        var total=0;
//        kartik
        var totalOt=0;
        var totalHr=0;
//        kartik

        var error=false;
        for(j=0;j<rows-1;j++){


            if((/^[0-9]+\.?[0-9]?[0-9]?$/).test($("#initialRows_"+j+"_"+id).val())) {
                var temp=parseFloat($("#initialRows_"+j+"_"+id).val());

                total=total+temp;
            } else if ($("#initialRows_"+j+"_"+id).val() == '') {
            	total=total;
            } else{
                var temp = $("#initialRows_"+j+"_"+id).val().split(":");
                temp[0]= parseFloat(temp[0]);
                temp[1]= parseFloat(temp[1])

                total=total+(temp[0]*3600+temp[1]*60)/3600;
            }

    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:validation
        Keep Original line here if:-
    */
             if((/^[0-9]+\.?[0-9]?[0-9]?$/).test($("#initialRows_ot_"+j+"_"+id).val())) {
                var tempOt=parseFloat($("#initialRows_ot_"+j+"_"+id).val());

                totalOt=totalOt+tempOt;
            } else if ($("#initialRows_ot_"+j+"_"+id).val() == '') {
            	totalOt=totalOt;
            } else{
                var tempOt = $("#initialRows_ot_"+j+"_"+id).val().split(":");
                tempOt[0]= parseFloat(tempOt[0]);
                tempOt[1]= parseFloat(tempOt[1])

                totalOt=totalOt+(tempOt[0]*3600+tempOt[1]*60)/3600;
            }

            totalHr=total+totalOt;

/*================================================================================*/
        }

        if(totalHr>24){

            error=true;


        }


        return !error;
    }



    /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose: For validation
        Keep Original line here if:-
    */
    function validateVerticalTotalOt(id){
//    alert("validateVerticalTotalOt..");
        var total=0;

        var error=false;
        for(j=0;j<rows-1;j++){


            if((/^[0-9]+\.?[0-9]?[0-9]?$/).test($("#initialRows_ot_"+j+"_"+id).val())) {
                var temp=parseFloat($("#initialRows_ot_"+j+"_"+id).val());

                total=total+temp;
            } else if ($("#initialRows_ot_"+j+"_"+id).val() == '') {
            	total=total;
            } else{
                var temp = $("#initialRows_ot_"+j+"_"+id).val().split(":");
                temp[0]= parseFloat(temp[0]);
                temp[1]= parseFloat(temp[1])

                total=total+(temp[0]*3600+temp[1]*60)/3600;
            }
        }

        if(total>24){

            error=true;
        }


        return !error;
    }

    function validateVerticalTotal_worker(id){
//    alert("2nd");
        var total=0;
        var emp_work_hours = $('#emp_work_hours').val();
        var error=false;
        for(j=0;j<rows-1;j++){


            if((/^[0-9]+\.?[0-9]?[0-9]?$/).test($("#initialRows_"+j+"_"+id).val())) {
                var temp=parseFloat($("#initialRows_"+j+"_"+id).val());

                total=total+temp;
            } else if ($("#initialRows_"+j+"_"+id).val() == '') {
            	total=total;
            } else{
                var temp = $("#initialRows_"+j+"_"+id).val().split(":");
                temp[0]= parseFloat(temp[0]);
                temp[1]= parseFloat(temp[1])

                total=total+(temp[0]*3600+temp[1]*60)/3600;
            }
        }

        if(total>emp_work_hours){
            error=true;
        }
        return !error;
    }










    function validateVerticalTotal_chk_st(id){
//    alert("2nd");
        var total=0;
        var emp_work_hours = $('#emp_work_hours').val();
        var error=false;
        for(j=0;j<rows-1;j++){


            if((/^[0-9]+\.?[0-9]?[0-9]?$/).test($("#initialRows_"+j+"_"+id).val())) {
                var temp=parseFloat($("#initialRows_"+j+"_"+id).val());

                total=total+temp;
            } else if ($("#initialRows_"+j+"_"+id).val() == '') {
            	total=total;
            } else{
                var temp = $("#initialRows_"+j+"_"+id).val().split(":");
                temp[0]= parseFloat(temp[0]);
                temp[1]= parseFloat(temp[1])

                total=total+(temp[0]*3600+temp[1]*60)/3600;
            }

        }

        return total;
    }
/*================================================================================== */

    function validateRow() {

        var flag = true;
        $(".messageBalloon_success").remove();
        //$(".messageBalloon_failure").remove()
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");

            /**
        By:kartik gondalia
        Date:23-03-2013
        Purpose:change width
        Keep Original line here if:-
    */
        var errorStyle = "background-color:#FFDFDF; width: 120px;";
        var normalStyle = "background-color:#FFFFFF; width: 120px;";
        /*===============================================================*/

        var projectActivityElementArray = new Array();
        var index = 0;

        $('.projectActivity').each(function(){
            element = $(this);
            $(element).attr('style', normalStyle);
            if($(element).val()==-1){
                $('#validationMsg').html(please_select_an_activity);
                $(element).attr('style', errorStyle);
                flag = false;
            }
            projectActivityElementArray[index] = $(element);
            index++;
        });

        for(var i=0; i<projectActivityElementArray.length; i++){
            var currentElement = projectActivityElementArray[i];
            for(var j=1+i; j<projectActivityElementArray.length; j++){
                if(currentElement.val() == projectActivityElementArray[j].val() ){
                    currentElement.attr('style', errorStyle);
                    $('#validationMsg').html(rows_are_duplicate);
                    projectActivityElementArray[j].attr('style', errorStyle);
                    flag = false;
                }
            }
        }

        return flag;
    }

    $('.projectActivity').bind('change',(function() {

        var flag = validateRow();
        if(!flag) {
            $('#btnSave').attr('disabled', 'disabled');
            $('#btnSave').attr('background', 'grey')
            $('#validationMsg').attr('class', "messageBalloon_failure");
        }
        else{
            $('#btnSave').removeAttr('disabled');
        }

    }));

    function validateProject() {

        var flag = true;
        $(".messageBalloon_success").remove();
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");

        var errorStyle = "background-color:#FFDFDF;";
        var normalStyle = "background-color:#FFFFFF;";
        var projectCount = projectsArray.length;
        $('input.project').each(function(){
            element = $(this);
            $(element).attr('style', normalStyle);
            proName = $.trim($(element).val()).toLowerCase();
            var temp = false;
            var i;
            for (i=0; i < projectCount; i++) {
                arrayName = projectsArray[i].name.toLowerCase().replace("##", "");
                arrayName = $("<div/>").html(arrayName).text();
                if (proName == arrayName) {

                    temp = true;
                    break;
                }
            }

            if(!temp){
                $('#validationMsg').html(project_name_is_wrong);
                $(element).attr('style', errorStyle);
                flag = false;
            }
        });
        return flag;
    }

    $('#timeComment').keyup(function() {


        var flag = validateTimehseetItemComment();
        if(!flag) {
            $('#commentSave').attr('disabled', 'disabled');
        //$('#validationMsg').attr('class', "messageBalloon_failure");
        }
        else{
            $('#commentSave').removeAttr('disabled');
            $('#commentCancel').removeAttr('disabled');
            $("#timeComment").removeAttr('style');
        }

    });

    $('.commentIcon').click(function(){

        $("#commentError").html("");
        $("#timeComment").val("");
        classStr = $(this).attr("id").split("_");
        deleteStr = $(this).attr("class").split(" ");

        if(deleteStr[1] == "deletedRow"){
            $("#timeComment").attr("disabled", "disabled")
            $("#commentSave").hide()
        }else{
            $("#timeComment").removeAttr("disabled")
            $("#commentSave").show()
        }
        var rowNo = classStr[2];
        date = currentWeekDates[classStr[1]];
        var activityNameId = "initialRows_"+rowNo+"_projectActivityName";
        activityId = $("#"+activityNameId).val();
        var comment = getComment(timesheetId,activityId,date,employeeId);

        $("#timeComment").val(comment);
        var projectNameId = "initialRows_"+rowNo+"_projectName";
        var activityNameId = "initialRows_"+rowNo+"_projectActivityName";

        var projectName = $.trim($("#"+projectNameId).val()).toLowerCase();

        var errorStyle = "background-color:#FFDFDF;";
        var projectCount = projectsArray.length;
        var temp = false;
        var i;
        for (i=0; i < projectCount; i++) {
            arrayName = projectsArray[i].name.toLowerCase().replace("##", "");
            arrayName = $("<div/>").html(arrayName).text();

            if (projectName == arrayName) {
                temp = true;
                break;
            }
        }

        if($("#"+projectNameId).val()=="" || $("#"+projectNameId).val()=="Type for hints..." || $("#"+activityNameId).val()=='-1'){
            $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#validationMsg').html(lang_selectProjectAndActivity);
        } else if( temp==false){
            $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#validationMsg').html(lang_enterExistingProject);
        }else{
            $("#commentProjectName").text(":"+" "+$("#"+projectNameId).val());
            $("#commentActivityName").text(":"+" "+$("#"+activityNameId+" :selected").text());
            var parsedDate = $.datepicker.parseDate("yy-mm-dd", date);
            $("#commentDate").text(":"+" "+$.datepicker.formatDate(datepickerDateFormat, parsedDate));
            $("#commentDialog").dialog('open');
        }

    });


    function validateTimehseetItemComment(){

        errFlag1 = false;


        $('#commentError').html("");

        var errorStyle = "background-color:#FFDFDF;";

        if ($('#timeComment').val().length > 2000) {
            $('#commentSave').attr('disabled', 'disabled');
            $('#commentCancel').attr('disabled', 'disabled');
            //   $('#validationMsg').attr('class', "messageBalloon_failure");
            $('#commentError').html(erorrMessageForInvalidComment);
            $('#timeComment').attr('style', errorStyle);

            errFlag1 = true;
        }

        return !errFlag1;


    }

    function saveComment(timesheetId,activityId,date,comment,employeeId) {

        var data = 'timesheetId=' + timesheetId + '&activityId=' + activityId + '&date=' + date+ '&comment=' + encodeURIComponent(comment)+ '&employeeId=' + employeeId;

        var r=$.ajax({
            type: 'POST',
            url: commentlink,
            data: data,
            async: false
        }).responseText;
        return r;

    }

    function getComment(timesheetId, activityId, date, employeeId){

        var r = $.ajax({
            type: 'POST',
            url: linkToGetComment,
            data: "timesheetId="+timesheetId+"&activityId="+activityId+"&date="+date+"&employeeId="+employeeId,
            async: false,
            success: function(comment){
                cmnt= comment;
            }
        });
        return cmnt;
    }

});





