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
echo "<html><head><title>Change Password</title></head><body bgcolor=\"#d4d4d4\">";

$OLDPASS  = trim($_POST['oldpassword']);
$NEWPASS1 = trim($_POST['newpassword1']);
$NEWPASS2 = trim($_POST['newpassword2']);

if ($NEWPASS1 !== $NEWPASS2) {
    echo "<font color=red><center>New passwords do not match</center></font>";
    exit;
}

// 1. Fetch the current hashed password safely
$stmt = $dbconnect->prepare("SELECT PASSWORD FROM xray_user WHERE ID = ?");
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<font color=red><center>User not found</center></font>";
    exit;
}

$currentHash = $row['PASSWORD'];

// 2. Verify old password
if (!password_verify($OLDPASS, $currentHash)) {
    echo "<font color=red><center>Wrong Old Password</center></font>";
    exit;
}

// 3. Hash new password
$newHash = password_hash($NEWPASS1, PASSWORD_DEFAULT);

// 4. Update database with secure hash
$stmt = $dbconnect->prepare("UPDATE xray_user SET PASSWORD = ? WHERE ID = ?");
$stmt->bind_param("si", $newHash, $userid);
$stmt->execute();

// 5. Log password change
$URL = $_SERVER["HTTP_REFERER"];
$IP  = $_SERVER["REMOTE_ADDR"];

$log = $dbconnect->prepare(
    "INSERT INTO xray_log (USER, IP, EVENT, URL) VALUES (?, ?, 'CHANGEPASSWORD', ?)"
);
$log->bind_param("sss", $userlogin, $IP, $URL);
$log->execute();

echo "<center>Password Changed<br>Please log out and log in again.</center>";
echo "</body></html>";
exit;


$sql = "UPDATE xray_user SET PASSWORD = '$NEWPASS1' WHERE LOGIN = '$userlogin'";
mysqli_query($dbconnect, $sql);
/////////////INSERT LOG/////////////
$URL=$_SERVER["HTTP_REFERER"];
mysqli_query($dbconnect, "insert into xray_log (USER,IP,EVENT,URL)VALUES ('$userlogin','$IP','CHANGEPASSWORD','$URL')");
echo "PASSWORD Changed";
echo "<br> Please LogOut and LogIN";
exit;
?>
<CENTER>
<FORM>
<INPUT type="button" value="Close Window" onClick="window.close()">
</FORM>
</CENTER>
</body>
</htmL>