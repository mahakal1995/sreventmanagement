<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <!--<link href="../themes/orange/css/style.css" rel="stylesheet" type="text/css">-->


        <!--<link href="../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css"/>-->

<!--<script type="text/javascript" src="../scripts/jquery/ui/ui.core.js"></script>-->
<!--<script type="text/javascript" src="../scripts/jquery/ui/ui.datepicker.js"></script>-->
<!--<script type="text/javascript" src="../scripts/jquery/jquery-1.3.2.js"></script>-->

         <?echo"<pre>";print_r($insert_ot_config);echo"</pre>";?>

        <script type="text/javascript" src="html/js/jquery-1.8.2.js"></script>
        <script type="text/javascript" src="html/js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="html/js/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="html/js/jquery.ui.datepicker.js"></script>
        <script type="text/javascript" src="html/js/jquery.validate.js"></script>
        <script type="text/javascript" src="html/js/jquery.ui.datepicker.validation.js"></script>
        <!--<link rel="stylesheet" href="html/images/demos.css">-->


        <link rel="stylesheet" href="html/images/jquery.ui.base.css">
        <link rel="stylesheet" href="html/images/jquery.ui.theme.css">
        <link rel="stylesheet" href="html/images/jquery.ui.button.css">
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/south-street/jquery-ui.css">

<script>
    
    function validate()
    {
//        alert("validate");
        if($( "#start_date" ).val() > $( "#end_date" ).val())
            {
              alert("From date should  be less than To date");
              return false;
            }
        return true;
    }
                    $(function() {
                        $( "#start_date" ).datepicker({
                            showOn: "button",
                            dateFormat: 'yy/mm/dd',
                            buttonImage: "html/images/calendar.gif",
                            buttonImageOnly: true
                        });
                
                
                        $( "#end_date" ).datepicker({
                            showOn: "button",
                            dateFormat: 'yy/mm/dd',
                            buttonImage: "html/images/calendar.gif",
                            buttonImageOnly: true
                        });

//                        $('#OtConfig').validate({ 
//                            errorPlacement: $.datepicker.errorPlacement, 
//                            rules: { 
//                                validDefaultDatepicker: { 
//                                    required: true, 
//                                    dpDate: true 
//                                }, 
//                                start_date: { 
//                                    dpCompareDate: ['before', '#end_date'] 
//                                }, 
//                                end_date: { 
//                                    dpCompareDate: {'after': '#start_date'} 
//                                }
//                            }, 
//                            messages: { 
//        
//                                end_date: 'To date should be Greater than From date' ,
//                                start_date: 'From date should  be less than To date' 
//                            }});
                                            
                    });

                </script>







        <style>
            table.data-table tr.even td {
                background-color: #EEEEEE;            
            }
        </style>


        <style>
            .table1 { 
                display:block;         
                height: 150px;
                width: 900px;
                overflow-y:auto;
                overflow-x:hidden;
                margin-left: 200px;
                
                
            }
        </style>
    </head>
    <body style="padding-left:4; padding-right:4;">
        <p>
<?if(isset($edit_ot_config[0]['id'])){$id=$edit_ot_config[0]['id'];}?>
        <form name="OtConfig" id="OtConfig" method="post" onsubmit="return validate();"action="<? if(isset($edit_ot_config)){ echo "index.php?action=edit_otconfig&edit=$id";}else{echo "index.php?action=edit_otconfig";}?>">
            <table cellpadding="0" cellspacing="0" width="95%" style="margin-top:10px;border:2px #FAD163 solid;border-radius: 5px;margin-left:11px;">
                <tr style="height:33px;background-color:#FAD163;">
                    <td colspan="3"style="font-weight: bold;font-size: 18px;font-family:arial;padding-left:10px;">
                        OT Configuration
                    </td>
                </tr>
                    
                <tr>
                    
                    <td  valign="top" align="center"  style="padding-top: 20px;">
                    
                        <fieldset style="border:#FAD163 solid 1px;width:1050px;margin-bottom:30px;">               
                            <legend style="font-weight: bold;">OT Config</legend>
                            
                            <table class="table1" cellpadding="3"  cellspacing="0">
                            
                                <tr style="font-family: Arial,Verdana,Helvetica,sans-serif;font-size:14px;color: #444444;">
                                    <td width="100" align="left">From date:</td>
                                    <td width="500" align="left"><input type="text" required  value="<? if(isset($edit_ot_config))echo $edit_ot_config[0]['start_date'];?>"name="start_date" id="start_date"></td>
                                </tr> 
                                <tr style="  font-family: Arial,Verdana,Helvetica,sans-serif;font-size:14px;color: #444444;;">
                                <td width="100" align="left">To date:</td>
                                <td width="500" align="left"><input type="text" required value="<? if(isset($edit_ot_config))echo $edit_ot_config[0]['end_date'];?>" name="end_date" id="end_date"></td>
                                </tr>
                                <tr style="  font-family: Arial,Verdana,Helvetica,sans-serif;font-size:14px;color: #444444;;">      
                                    <td width="100" align="left">Multiply:</td>
                                    <td width="500" align="left"><input type="text" required value="<? if(isset($edit_ot_config))echo $edit_ot_config[0]['multiply'];?>" name="multiply" id="multiply"></td>
                                </tr>   
                                
                                <tr>      
                                
                                    <td width="100" align="left"> <input type="submit" style="background-color:#999966;font-family: Arial,Verdana,Helvetica,sans-serif;color: white;font-weight: bold;" id="submit" name="submit" value="<? if(isset($edit_ot_config)){echo "Edit";}else{echo "Add";}?>" ></td>
                                </tr>                                
                            </table>
                                                                            
                        </fieldset>
                        
                        
                          <table  cellpadding="0" cellspacing="0" width="85%" style="margin-bottom:20px;margin-top:10px;border:2px #FAD163 solid;border-radius: 5px;margin-left:11px;">
                            
                                <tr style="height:33px;background-color:#FAD163;font-weight: bold;font-size:14px; color:#4C4228;font-family:arial;">
                                    <td style="padding-left:9px;">Start Date</td>
                                    <td style="padding-left:9px;">End Date</td>
                                    <td>Multiply</td>
                                    <td style="padding-left:9px;">Action</td>                                    
                                </tr>
                                
                                
                                
                                <?php

                                if ((isset($list_ot_config)) && ($list_ot_config !='')) {
                                   

                                for ($j=0; $j<count($list_ot_config);$j++) {

                                ?>
                                <tr style="font-size:12px;">
                                    <? if(!($j%2)) {
//                                        echo "jiii".$j;
                                        ?>   
                                     <td style="line-height: 30px; background-color: #F5DEB3;width:320px;text-align:left;">&nbsp;&nbsp;<?php echo $list_ot_config[$j]['start_date'];?></td>
                                     <td style="line-height: 30px; background-color: #F5DEB3;width:320px;text-align:left;">&nbsp;&nbsp;<?php echo $list_ot_config[$j]['end_date'];?></td>
                                     <td style="line-height: 30px; background-color: #F5DEB3;width:200px;text-align:left;">&nbsp;&nbsp;<?php echo $list_ot_config[$j]['multiply'];?></td>
                                     <td style="line-height: 30px; background-color: #F5DEB3;width:200px;text-align:left;">&nbsp;&nbsp;<a href="index.php?action=edit_otconfig&edit=<?php echo $list_ot_config[$j]['id']; ?>"><img style="width:20px;height:15px;" src="../inextrix/html/images/edit.png"></a> | <a href="index.php?action=del_otconfig&del=<?php echo $list_ot_config[$j]['id']; ?>"  onclick="return confirm('Are you sure?')"><img style="padding-top:2px; width:20px;height:18px;" src="../inextrix/html/images/delete.png"></a></td>
                                     
                                     <?} else{?>
                                    
                                     
                                      <td style="line-height: 30px; background-color: #FFFAE3;width:120px;text-align:left;">&nbsp;&nbsp;<?php echo $list_ot_config[$j]['start_date'];?></td>
                                      <td style="line-height: 30px; background-color: #FFFAE3;width:120px;text-align:left;">&nbsp;&nbsp;<?php echo $list_ot_config[$j]['end_date'];?></td>
                                      <td style="line-height: 30px; background-color: #FFFAE3;width:120px;text-align:left;">&nbsp;&nbsp;<?php echo $list_ot_config[$j]['multiply'];?></td>
                                      <td style="line-height: 30px; background-color: #FFFAE3;width:120px;text-align:left;">&nbsp;&nbsp;<a href="index.php?action=edit_otconfig&edit=<?php echo $list_ot_config[$j]['id']; ?>"><img style="width:20px;height:15px;" src="../inextrix/html/images/edit.png"></a> | <a href="index.php?action=del_otconfig&del=<?php echo $list_ot_config[$j]['id']; ?>"  onclick="return confirm('Are you sure?')"><img style="padding-top:2px; width:20px;height:18px;" src="../inextrix/html/images/delete.png"></a></td>
                                     
                                     <?}?>
                                </tr>
                                <?}}?>         

                                                                                                
                            </table>                              
                    </td>                    
                </tr>
                    
            </table>

        </form>

    </body> 
