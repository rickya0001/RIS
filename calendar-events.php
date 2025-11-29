<?php
# ThaiRIS Calendar Events
# Fetches appointments for FullCalendar
include ("connectdb.php");
include ("session.php");
header('Content-Type: application/json');

$start = mysqli_real_escape_string($dbconnect, $_GET['start']);
$end = mysqli_real_escape_string($dbconnect, $_GET['end']);

// Note: xray_schedule table doesn't have an ID field, so we'll use a combination
// of fields to create a unique identifier for each event
$sql = "SELECT 
    s.HN as MRN,
    s.NAME,
    s.LASTNAME,
    s.DATE,
    s.TIME_START,
    s.TIME_END,
    s.ROOM,
    s.XRAY_CODE,
    s.WARD,
    x.DESCRIPTION as PROCEDURE_NAME
FROM xray_schedule s
LEFT JOIN xray_code x ON s.XRAY_CODE = x.XRAY_CODE AND x.CENTER = '$center_code'
WHERE s.DATE BETWEEN '$start' AND '$end'
ORDER BY s.DATE, s.TIME_START";

$result = mysqli_query($dbconnect, $sql);
$events = array();

if ($result) {
    while($row = mysqli_fetch_array($result)) {
        // Create a unique ID from date, time, and MRN
        $eventId = md5($row['DATE'] . $row['TIME_START'] . $row['MRN']);
        
        $events[] = array(
            'id' => $eventId,
            'title' => $row['NAME'] . ' ' . $row['LASTNAME'] . ' - ' . ($row['PROCEDURE_NAME'] ? $row['PROCEDURE_NAME'] : $row['XRAY_CODE']),
            'start' => $row['DATE'] . 'T' . $row['TIME_START'],
            'end' => $row['DATE'] . 'T' . $row['TIME_END'],
            'extendedProps' => array(
                'patientName' => $row['NAME'] . ' ' . $row['LASTNAME'],
                'mrn' => $row['MRN'],
                'room' => $row['ROOM'],
                'procedure' => $row['XRAY_CODE'],
                'procedureName' => $row['PROCEDURE_NAME'],
                'ward' => $row['WARD']
            )
        );
    }
}

echo json_encode($events);
?>

