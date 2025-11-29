<?php
# ThaiRIS (Thai Radiology Information System)
# Version: 1.8
# File last modified: Calendar Integration
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
include ("session.php");
include ("connectdb.php");
header("Content-type: text/html;  charset=utf-8");
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Appointment Calendar - Thai RIS</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- jQuery (latest version) -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- FullCalendar CSS (latest version 6.x) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
<!-- FullCalendar JS (latest version 6.x) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<style>
#calendar {
    max-width: 100%;
    margin: 20px auto;
    background: white;
    padding: 20px;
    border-radius: 5px;
}
#patient-results {
    margin-top: 10px;
    max-height: 200px;
    overflow-y: auto;
}
#patient-results table {
    width: 100%;
    border-collapse: collapse;
}
#patient-results table td, #patient-results table th {
    padding: 5px;
    border: 1px solid #ddd;
}
.selected-patient {
    background: #d4edda;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    color: #155724;
}
.modal-header {
    background-color: #007bbc;
    color: white;
}
.modal-header .btn-close {
    filter: invert(1);
}
</style>
</head>
<body>
<div class="container-fluid mt-4">
    <h2 class="mb-4">Appointment Calendar</h2>
    
    <!-- Patient Search Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Search Patient to Book Appointment</h5>
        </div>
        <div class="card-body">
            <form id="search-patient-form" method="post">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label"><font face="MS Sans Serif">MRN:</font></label>
                        <input type="text" class="form-control" name="mrn" id="search_mrn" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><font face="MS Sans Serif">Name:</font></label>
                        <input type="text" class="form-control" name="fname" id="search_fname" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><font face="MS Sans Serif">Lastname:</font></label>
                        <input type="text" class="form-control" name="lname" id="search_lname" />
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" onclick="searchPatients()">Search</button>
                    </div>
                </div>
            </form>
            <div id="patient-results" class="mt-3"></div>
            <div id="selected-patient-display"></div>
        </div>
    </div>
    
    <!-- FullCalendar Container -->
    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
    
    <!-- Appointment Booking Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">Book Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="appointment-form">
                        <input type="hidden" id="appointment_mrn" />
                        <input type="hidden" id="appointment_date" />
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">Patient:</font></label>
                            </div>
                            <div class="col-md-8">
                                <strong><span id="appointment_patient_name"></span></strong>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">Date:</font></label>
                            </div>
                            <div class="col-md-8">
                                <strong><span id="appointment_date_display"></span></strong>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">Start Time:</font></label>
                            </div>
                            <div class="col-md-8">
                                <input type="time" class="form-control" id="appointment_time_start" required />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">End Time:</font></label>
                            </div>
                            <div class="col-md-8">
                                <input type="time" class="form-control" id="appointment_time_end" required />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">Procedure:</font></label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select" id="appointment_procedure" required>
                                    <option value="">-- Select Procedure --</option>
                                    <?php
                                    $proc_result = mysqli_query($dbconnect, "SELECT XRAY_CODE, DESCRIPTION FROM xray_code WHERE CENTER='$center_code' AND ACTIVE=1 ORDER BY DESCRIPTION");
                                    while($proc = mysqli_fetch_array($proc_result)) {
                                        echo "<option value='".$proc['XRAY_CODE']."'>".$proc['DESCRIPTION']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">Room:</font></label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select" id="appointment_room" required>
                                    <option value="">-- Select Room --</option>
                                    <?php
                                    $room_result = mysqli_query($dbconnect, "SELECT NAME, DESCRIPTION FROM xray_room WHERE CENTER='$center_code' ORDER BY NAME");
                                    while($room = mysqli_fetch_array($room_result)) {
                                        echo "<option value='".$room['NAME']."'>".$room['DESCRIPTION']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">Ward:</font></label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="appointment_ward" value="" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><font face="MS Sans Serif">Notes:</font></label>
                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control" id="appointment_notes" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAppointment()">Save Appointment</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Appointment Details Modal -->
    <div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentDetailsModalLabel">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Patient:</strong></div>
                        <div class="col-md-8" id="detail_patient_name"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>MRN:</strong></div>
                        <div class="col-md-8" id="detail_mrn"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Procedure:</strong></div>
                        <div class="col-md-8" id="detail_procedure"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Room:</strong></div>
                        <div class="col-md-8" id="detail_room"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Time:</strong></div>
                        <div class="col-md-8" id="detail_time"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="text-success" style="font-size: 4rem; line-height: 1;">✓</div>
                        <p class="mt-3" id="successMessage"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="text-danger" style="font-size: 4rem; line-height: 1;">✕</div>
                        <p class="mt-3" id="errorMessage"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Warning Modal -->
    <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="warningModalLabel">Warning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="text-warning" style="font-size: 4rem; line-height: 1;">⚠</div>
                        <p class="mt-3" id="warningMessage"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var calendar;
var selectedPatient = null;

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // Fetch appointments from database
            jQuery.ajax({
                url: 'calendar-events.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                },
                success: function(response) {
                    successCallback(response);
                },
                error: function() {
                    failureCallback();
                }
            });
        },
        dateClick: function(info) {
            if (selectedPatient) {
                openAppointmentModal(info.dateStr, selectedPatient);
            } else {
                showWarningModal('Please search and select a patient first');
            }
        },
        eventClick: function(info) {
            // Show appointment details in Bootstrap modal
            var patientName = info.event.extendedProps.patientName || '';
            var mrn = info.event.extendedProps.mrn || '';
            var room = info.event.extendedProps.room || '';
            var procedure = info.event.extendedProps.procedure || info.event.extendedProps.procedureName || '';
            var startTime = info.event.start.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
            var endTime = info.event.end ? info.event.end.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'}) : '';
            
            jQuery('#detail_patient_name').text(patientName);
            jQuery('#detail_mrn').text(mrn);
            jQuery('#detail_procedure').text(procedure);
            jQuery('#detail_room').text(room);
            jQuery('#detail_time').text(startTime + (endTime ? ' - ' + endTime : ''));
            
            var detailsModal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
            detailsModal.show();
        },
        editable: true,
        eventDrop: function(info) {
            // Handle appointment rescheduling
            updateAppointment(info.event, info.revert);
        }
    });
    
    calendar.render();
});

function searchPatients() {
    var mrn = jQuery('#search_mrn').val();
    var fname = jQuery('#search_fname').val();
    var lname = jQuery('#search_lname').val();
    
    jQuery.ajax({
        url: 'calendar-patient-search.php',
        type: 'POST',
        data: {
            mrn: mrn,
            fname: fname,
            lname: lname,
            center_code: '<?php echo $center_code; ?>'
        },
        success: function(response) {
            jQuery('#patient-results').html(response);
        }
    });
}

function selectPatient(mrn, name, lastname) {
    selectedPatient = {
        mrn: mrn,
        name: name,
        lastname: lastname
    };
    jQuery('#selected-patient-display').html('<div class="selected-patient"><strong>Selected Patient:</strong> ' + name + ' ' + lastname + ' (MRN: ' + mrn + ')</div>');
}

function openAppointmentModal(dateStr, patient) {
    jQuery('#appointment_mrn').val(patient.mrn);
    jQuery('#appointment_date').val(dateStr);
    jQuery('#appointment_patient_name').text(patient.name + ' ' + patient.lastname);
    jQuery('#appointment_date_display').text(dateStr);
    
    // Reset form
    jQuery('#appointment_time_start').val('');
    jQuery('#appointment_time_end').val('');
    jQuery('#appointment_procedure').val('');
    jQuery('#appointment_room').val('');
    jQuery('#appointment_ward').val('');
    jQuery('#appointment_notes').val('');
    
    var appointmentModal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    appointmentModal.show();
}

function closeAppointmentModal() {
    var appointmentModal = bootstrap.Modal.getInstance(document.getElementById('appointmentModal'));
    if (appointmentModal) {
        appointmentModal.hide();
    }
}

function showSuccessModal(message) {
    jQuery('#successMessage').text(message);
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
}

function showErrorModal(message) {
    jQuery('#errorMessage').text(message);
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
}

function showWarningModal(message) {
    jQuery('#warningMessage').text(message);
    var warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
    warningModal.show();
}

function saveAppointment() {
    var data = {
        mrn: jQuery('#appointment_mrn').val(),
        date: jQuery('#appointment_date').val(),
        time_start: jQuery('#appointment_time_start').val(),
        time_end: jQuery('#appointment_time_end').val(),
        procedure: jQuery('#appointment_procedure').val(),
        room: jQuery('#appointment_room').val(),
        ward: jQuery('#appointment_ward').val(),
        notes: jQuery('#appointment_notes').val(),
        user_id: '<?php echo $usercode; ?>'
    };
    
    if (!data.time_start || !data.time_end) {
        showWarningModal('Please enter both start and end times');
        return;
    }
    
    if (!data.procedure) {
        showWarningModal('Please select a procedure');
        return;
    }
    
    if (!data.room) {
        showWarningModal('Please select a room');
        return;
    }
    
    jQuery.ajax({
        url: 'calendar-save.php',
        type: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                showSuccessModal('Appointment saved successfully!');
                calendar.refetchEvents();
                closeAppointmentModal();
                // Clear selected patient
                selectedPatient = null;
                jQuery('#selected-patient-display').html('');
            } else {
                showErrorModal('Error: ' + response.message);
            }
        },
        dataType: 'json',
        error: function() {
            showErrorModal('Error saving appointment. Please try again.');
        }
    });
}

function updateAppointment(event, revertFunc) {
    jQuery.ajax({
        url: 'calendar-update.php',
        type: 'POST',
        data: {
            id: event.id,
            start: event.startStr,
            end: event.endStr
        },
        success: function(response) {
            if (!response.success) {
                showErrorModal('Error updating appointment: ' + (response.message || 'Unknown error'));
                if (revertFunc) revertFunc();
            } else {
                showSuccessModal('Appointment updated successfully!');
            }
        },
        dataType: 'json',
        error: function() {
            showErrorModal('Error updating appointment');
            if (revertFunc) revertFunc();
        }
    });
}
</script>
</body>
</html>

