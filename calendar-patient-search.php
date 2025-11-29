<?php
# ThaiRIS Calendar Patient Search
# Searches for patients to book appointments
include ("connectdb.php");
include ("session.php");

$mrn = trim($_POST['mrn']);
$fname = trim($_POST['fname']);
$lname = trim($_POST['lname']);
$center_code = mysqli_real_escape_string($dbconnect, $_POST['center_code']);

// Escape inputs for security
$mrn = mysqli_real_escape_string($dbconnect, $mrn);
$fname = mysqli_real_escape_string($dbconnect, $fname);
$lname = mysqli_real_escape_string($dbconnect, $lname);

$sql = "SELECT MRN, NAME, LASTNAME, BIRTH_DATE FROM xray_patient_info 
        WHERE (MRN LIKE '%$mrn%') 
        AND (NAME LIKE '%$fname%') 
        AND (LASTNAME LIKE '%$lname%') 
        AND (CENTER_CODE ='$center_code') 
        LIMIT 0,20";

$result = mysqli_query($dbconnect, $sql);
$num_rows = mysqli_num_rows($result);

if ($num_rows > 0) {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped table-hover table-bordered'>";
    echo "<thead class='table-light'><tr><th><font face='MS Sans Serif'>MRN</font></th><th><font face='MS Sans Serif'>Name</font></th><th><font face='MS Sans Serif'>Lastname</font></th><th><font face='MS Sans Serif'>Action</font></th></tr></thead>";
    echo "<tbody>";
    while($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['MRN']) . "</td>";
        echo "<td>" . htmlspecialchars($row['NAME']) . "</td>";
        echo "<td>" . htmlspecialchars($row['LASTNAME']) . "</td>";
        $mrn_escaped = htmlspecialchars($row['MRN'], ENT_QUOTES);
        $name_escaped = htmlspecialchars($row['NAME'], ENT_QUOTES);
        $lname_escaped = htmlspecialchars($row['LASTNAME'], ENT_QUOTES);
        echo "<td><button class='btn btn-sm btn-primary' onclick='selectPatient(\"".$mrn_escaped."\", \"".$name_escaped."\", \"".$lname_escaped."\")'>Select</button></td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "<div class='alert alert-warning'><font face='MS Sans Serif'>No patients found</font></div>";
}
?>

