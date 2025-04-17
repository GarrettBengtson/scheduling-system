<?php
// Include connection to db file
require_once "connection.php";

class InstructorQuery {
    
    /*
     * Gets all available (isAvailable = 1) time slots based 
     * on the inputted date.
     * instructorID should be passed in through cookies/the
     * logged in user's id.
    */
    public function getAvailableTimeSlotsByDate($instructorID, $date){
        return "SELECT * FROM Time_Slots AS "
            + " WHERE instructorID = $instructorID"
            + " AND isAvailable = 1"
            + " AND date = '$date';";
    }

    /*
     * Gets all scheduled appointments (isAvailable = 0) based
     * on the inputted date. Also shows the group name of the group
     * tied to the appointment. 
     * instructorID should be passed in through cookies/the
     * logged in user's id.
    */ 
    public function getScheduledAppointmentsByDate($instructorID, $date){
        return "SELECT g.id AS groupID, g.projectName, ts.startTime, ts.endTime, ts.date, a.id AS appointmentID"
            + " FROM Time_Slots AS ts"
            + " INNER JOIN Appointment AS a"
            + " ON a.timeSlotID = ts.id"
            + " INNER JOIN Group AS g"
            + " ON g.id = a.groupID"
            + " WHERE instructorID = $instructorID"
            + " AND date = '$date';";
    }

    /*
     * Gets all appointments occurring after the current moment.
     * instructorID should be passed in through cookies/the
     * logged in user's id.
    */ 
    public function getAllFutureAppointments($instructorID){
        return "SELECT g.projectName, ts.startTime, ts.endTime, ts.date, a.id AS appointmentID"
            + " FROM Time_Slots AS ts"
            + " INNER JOIN Appointment AS a"
            + " ON a.timeSlotID = ts.id"
            + " INNER JOIN Group AS g"
            + " ON g.id = a.groupID"
            + " WHERE instructorID = $instructorID"
            + " AND date >= NOW();";
    }

    /*
     * Deletes the selected time slot entry and any
     * appointment connected to it.
    */ 
    public function deleteTimeSlot($timeSlotID){
        return "DELETE FROM Appointment"
            + " WHERE timeSlotID = $timeSlotID;"

            + " DELETE FROM Time_Slots"
            + " WHERE id = $timeSlotID;";
    }

    /*
     * Deletes the selected appointment entry and sets
     * isAvailable of the correlating Time_Slot entry
     * to 1. Useful if a student tells the professor
     * they can't make it to an appointment.
    */ 
    public function cancelGroupAppointment($appointmentID){
        return "UPDATE Time_Slots AS ts"
            + " JOIN Appointment AS a"
            + " ON ts.id = a.timeSlotID"
            + " SET isAvailable = 1"
            + " WHERE a.id =$appointmentID;"

            + " DELETE FROM Appointment"
            + " WHERE id = $appointmentID;";
    }

    /*
     * Creates a new entry in the Appointment table. Input
     * needs to be gathered for Group id, Time_Slot id
     * by selecting from existing groups and time slots.
     * Set isAvailable of selected Time_Slot entry to 0.
    */ 
    public function setupAppointment($timeSlotID, $groupID){
        return "INSERT INTO Appointment"
            + " (timeSlotID, groupID)"
            + " VALUES ($timeSlotID, $groupID);"

            + " UPDATE Time_Slots"
            + " SET isAvailable = 0"
            + " WHERE id = $timeSlotID;";
    }

    /*
     * Creates a new Time_Slot entry. Input validation needs
     * to be handled in the form for getting the times and date.
     * HH:MM:SS format for times. YYYY-MM-DD for date.
     * Pass in the instructorID using cookies of the logged in
     * user. isAvailable is set to 1 because the Time Slot will 
     * inherently be available.
    */
    public function createTimeSlot($instructorID, $startTime, $endTime, $date){
        return "INSERT INTO Time_Slots"
            + " (instructorID, startTime, endTime, date, isAvailable)"
            + " VALUES ($instructorID, '$startTime',"
            + " '$endTime', '$date', 1);";
    }

    /*
     * List group information based on the inputted groupID.
    */
    public function getGroupInfo($groupID){
        return "SELECT u.username, u.email, g.projectName, IF(u.id = g.groupLeaderID, 'True', 'False') AS isGroupLeader"
            + " FROM Group AS g"
            + " INNER JOIN Group_Association AS ga"
            + " ON g.id = ga.groupID"
            + " INNER JOIN User AS u"
            + " ON u.id = ga.userID"
            + " WHERE g.id = $groupID";
    }

}