<?php
// Include connection to db file
require_once "connection.php";

class StudentQuery {
    //Generic retrieval of available time slots.
    public function getAvailableTimeSlots(){
        return "SELECT ts.id AS timeSlotID, ts.date, ts.startTime, ts.endTime, ts.isAvailable, instructor.email AS instructorEmail" 
            + " FROM Time_Slots AS ts"
            + " INNER JOIN User AS instructor"
            + " ON u.id = ts.instructorID"
            + " WHERE isAvailable = 1;";
    }

    //Gets information about current appointments of the logged in student
    //Utilize cookies to send the userID while calling this statement.
    public function getCurrentAppointments($userID){
        return "SELECT a.id AS appointmentID, g.projectName, g.id AS groupID, ts.date, ts.startTime, ts.endTime, instructor.email AS instructorEmail"
            + " FROM Group AS g"
            + " INNER JOIN Group_Association AS ga"
            + " ON ga.groupID = g.id"
            + " INNER JOIN User AS u"
            + " ON u.id = ga.userID"
            + " INNER JOIN Appointment AS a"
            + " ON a.groupID = g.id"
            + " INNER JOIN Time_Slots AS ts"
            + " ON ts.id = a.timeSlotID"
            + " INNER JOIN User AS instructor"
            + " ON instructor.id = ts.instructorID"
            + " WHERE u.id = $userID;";
    }

    /*
     * Deletes the selected appointment entry and sets the according
     * Time_Slots entry's isAvailable entry to 1 (true). The appointment
     * id should be retrieved before calling this method (Ideally by
     * having a cancel appointment button in each row of the 
     * getCurrentAppointments table that sends the appointmentID).
     * 
     * This works for our database because we essentially treat 
     * the Appointment table as a join table between Groups and 
     * Time_Slots.
    */
    public function cancelAppointment($appointmentID){
        return "UPDATE Time_Slots AS ts"
            + " JOIN Appointment AS a"
            + " ON ts.id = a.timeSlotID"
            + " SET isAvailable = 1"
            + " WHERE a.id =$appointmentID;"

            + " DELETE FROM Appointment"
            + " WHERE id = $appointmentID;";
    }

    /*
     * Schedules an appointment from the available time slots for
     * the inputted group. The timeSlotID and groupID should be 
     * retrieved before calling this method (for the timeSlotID, 
     * it should be a button in the row of the getAvailableTimeSlots
     * table that sends the timeSlotID to this function. for the
     * groupID, the user should be prompted to select one of their
     * existing groups after pressing that button. Get the groupID
     * from this.
    */
    public function scheduleAppointment($timeSlotID, $groupID){
        return "INSERT INTO Appointment"
            + " (timeSlotID, groupID)"
            + " VALUES ($timeSlotID, $groupID);"

            + " UPDATE Time_Slots AS ts"
            + " JOIN Appointment AS a"
            + " ON ts.id = a.timeSlotID"
            + " SET isAvailable = 0"
            + " WHERE ts.id = $timeSlotID;";
    }
}
?>