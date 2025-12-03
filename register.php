<?php
# ThaiRIS (Thai Radiology Information System)
# Version: 1.8
# File last modified: 4-Oct 2020
# File name: 
# http://www.thairis.net
# By ThaiRIS.Net
# Email : info.xraythai@gmail.com
##############################################################################
# COPYRIGHT NOTICE                                                           
# Copyright 2009-2024 ThaiRIS All Rights Reserved.              
#                                                                            
# This script may be used and modified free of charge by anyone so long as   
# this copyright notice and the comments above remain intact. By using this  
# code you agree to indemnify ThaiRIS.net from any liability that might
# arise from it's use.                                                       
#                                                                            
# Selling the code for this program without prior written consent is         
# expressly forbidden. In other words, please ask first before you try and   
# make money off this program.                                               
#                                                                            
# Obtain permission before redistributing this software over the Internet or 
# in any other medium. In all cases copyright and header must remain intact. 
# This Copyright is in full effect in any country that has International     
##############################################################################
include "session.php";
header("Content-type: text/html;  charset=utf-8");
if ($create_order == 0)
	{
		echo "<body bgcolor=#E8E8E8 topmargin=0 leftmargin=0>";
		$topbar = "Registration";
		include "topbar.php";
		echo "<br /><br /><br /> <center>Your Right now arrow create new order Please check with system admin.</center>";
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"8;URL=main.php\">";
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Registration</title>
   <meta http-equiv="Content-Type" content="text/html; charset=tis-620" />
  <link href="css/main.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui-1.7.2.custom.css">  
  <script language=JavaScript src="frames_body_array_<?php  echo $LANGUAGE ?>.js" type=text/javascript></script> 
</head>
<body>
  <header>
    Registration
    <div class="jclock"></div>
  </header>

  <search>
    <h1>Search Patient</h1>
    <form name="searchpatient" method="post" action="regis_search.php" accept-charset="UTF-8">
      
      <label for="search_mrn">MRN</label>
      <input type="text" name="mrn" id="search_mrn">

      <label for="search_fname">First Name</label>
      <input type="text" name="fname" id="search_fname" value="">

      <label for="search_lname">Last Name</label>
      <input type="text" name="lname" id="search_lname">

      <input type="submit" name="Submit" value="Search">
    </form>
  </search>

  <main>
    <h1>Create new patient</h1>
    <form name="form2" method="post" action="regis.php">
      
      <h2>Basic Information</h2>

      <label for="mrn_new">MRN</label>
      <input type="text" name="mrn" id="mrn_new" maxlength="10">

      <label for="xn">XN</label>
      <input type="text" name="xn" id="xn" maxlength="10">

      <label for="fname_new">First Name</label>
      <input type="text" name="fname" id="fname_new" maxlength="100">

      <label for="lname_new">Last Name</label>
      <input type="text" name="lname" id="lname_new" maxlength="100">

      <label for="mname_new">Middle Name</label>
      <input type="text" name="mname" id="mname_new" maxlength="100">

      <label for="id_number">ID Number</label>
      <input type="text" name="ID" id="id_number" size="20" maxlength="13">

      <label>Sex</label><br>
      <input type="radio" name="sex" value="M" id="sex_m">
      <label for="sex_m">Male</label>

      <input type="radio" name="sex" value="F" id="sex_f">
      <label for="sex_f">Female</label>

      <input type="radio" name="sex" value="U" id="sex_u">
      <label for="sex_u">Other</label><br><br>

      <label for="dob">Date of Birth</label>
      <input type="date" name="dob" id="dob" size="10" value="">

      <label for="ptweight">Weight</label>
      <input type="text" name="ptweight" id="ptweight">

      <label for="ptheight">Height</label>
      <input type="text" name="ptheight" id="ptheight">

      <input type="reset" name="Submit4" value="Clear">
      <input type="submit" name="Submit4" value="OK">

    </form>
  </main>
</body>
</html>

