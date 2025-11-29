<?php
# ThaiRIS Calendar Save Appointment
# Saves new appointments to the database
include ("connectdb.php");
include ("session.php");
include ("function.php");
header('Content-Type: application/json');

$mrn = mysqli_real_escape_string($dbconnect, $_POST['mrn']);
$date = mysqli_real_escape_string($dbconnect, $_POST['date']);
$time_start = mysqli_real_escape_string($dbconnect, $_POST['time_start']);
$time_end = mysqli_real_escape_string($dbconnect, $_POST['time_end']);
$procedure = mysqli_real_escape_string($dbconnect, $_POST['procedure']);
$room = mysqli_real_escape_string($dbconnect, $_POST['room']);
$ward = mysqli_real_escape_string($dbconnect, $_POST['ward']);
$notes = mysqli_real_escape_string($dbconnect, $_POST['notes']);
$user_id = mysqli_real_escape_string($dbconnect, $_POST['user_id']);

// Validate required fields
if (empty($mrn) || empty($date) || empty($time_start) || empty($time_end) || empty($procedure) || empty($room)) {
    echo json_encode(array('success' => false, 'message' => 'Missing required fields'));
    exit;
}

// Get patient info
$patient_sql = "SELECT NAME, LASTNAME, BIRTH_DATE FROM xray_patient_info WHERE MRN='$mrn' AND CENTER_CODE='$center_code'";
$patient_result = mysqli_query($dbconnect, $patient_sql);

if (!$patient_result || mysqli_num_rows($patient_result) == 0) {
    echo json_encode(array('success' => false, 'message' => 'Patient not found'));
    exit;
}

$patient = mysqli_fetch_array($patient_result);
$patient_name = $patient['NAME'];
$patient_lastname = $patient['LASTNAME'];
$birth_date = $patient['BIRTH_DATE'];

// Calculate age
$age = '';
if (!empty($birth_date)) {
    $ageCalc = AgeCal($birth_date);
    // Extract just the year part for the AGE field (xray_schedule expects varchar(3))
    if (preg_match('/(\d+)Y/', $ageCalc, $matches)) {
        $age = $matches[1];
    } else {
        $age = '0';
    }
} else {
    $age = '0';
}

// If ward is empty, set a default
if (empty($ward)) {
    $ward = '-';
}

// Insert into xray_schedule table
$sql = "INSERT INTO xray_schedule (HN, NAME, LASTNAME, AGE, WARD, DATE, TIME_START, TIME_END, ROOM, XRAY_CODE, USER_ID, DATE_REG, REASON_FOR_STUDY) 
        VALUES ('$mrn', '$patient_name', '$patient_lastname', '$age', '$ward', '$date', '$time_start', '$time_end', '$room', '$procedure', '$user_id', CURDATE(), '$notes')";

if (mysqli_query($dbconnect, $sql)) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false, 'message' => mysqli_error($dbconnect)));
}
?>

