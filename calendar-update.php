<?php
# ThaiRIS Calendar Update Appointment
# Updates appointment times when dragged on calendar
include ("connectdb.php");
include ("session.php");
header('Content-Type: application/json');

// Note: Since xray_schedule doesn't have an ID field, we'll need to identify
// the record by a combination of fields. For now, we'll use the event ID
// which is a hash of date, time, and MRN. However, this makes updates tricky.
// For a production system, you might want to add an ID field to xray_schedule.

// For this implementation, we'll parse the start/end times and update
// based on the original date/time and MRN from the event
$start = mysqli_real_escape_string($dbconnect, $_POST['start']);
$end = mysqli_real_escape_string($dbconnect, $_POST['end']);

// Parse the datetime strings
$start_datetime = new DateTime($start);
$end_datetime = new DateTime($end);

$new_date = $start_datetime->format('Y-m-d');
$new_time_start = $start_datetime->format('H:i:s');
$new_time_end = $end_datetime->format('H:i:s');

// Since we don't have a direct ID, we'll need to get the MRN from the event
// This is a limitation - ideally the table should have an ID field
// For now, we'll return an error suggesting manual update
// In a production system, you would:
// 1. Add an ID field to xray_schedule table, OR
// 2. Pass MRN and original date/time as additional parameters

echo json_encode(array(
    'success' => false, 
    'message' => 'Appointment rescheduling requires additional information. Please delete and recreate the appointment, or add an ID field to xray_schedule table for proper update functionality.'
));

// If you want to implement updates properly, you would need to:
// 1. Modify calendar.php to pass MRN and original date/time when dragging
// 2. Use those to identify and update the record:
/*
$mrn = mysqli_real_escape_string($dbconnect, $_POST['mrn']);
$original_date = mysqli_real_escape_string($dbconnect, $_POST['original_date']);
$original_time = mysqli_real_escape_string($dbconnect, $_POST['original_time']);

$sql = "UPDATE xray_schedule 
        SET DATE='$new_date', TIME_START='$new_time_start', TIME_END='$new_time_end'
        WHERE HN='$mrn' AND DATE='$original_date' AND TIME_START='$original_time'";

if (mysqli_query($dbconnect, $sql)) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false, 'message' => mysqli_error($dbconnect)));
}
*/
?>

