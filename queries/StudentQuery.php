<?php

namespace StudentQuery;

require_once "connection.php";


class StudentQuery
{

    public function __construct()
    {
    }

    public function getAvailableTimeSlots()
    {
        return "SELECT ts.id AS timeSlotID, ts.date, ts.startTime, ts.endTime, ts.isAvailable, instructor.email AS instructorEmail " .
            "FROM Time_Slots AS ts " .
            "INNER JOIN User AS instructor ON instructor.id = ts.instructorID " .
            "WHERE ts.isAvailable = 1;";
    }

    public function getCurrentAppointments($userID)
    {
        return "SELECT a.id AS appointmentID, g.projectName, g.id AS groupID, ts.date, ts.startTime, ts.endTime, instructor.email AS instructorEmail " .
            "FROM `Group` AS g " .
            "INNER JOIN Group_Association AS ga ON ga.groupID = g.id " .
            "INNER JOIN User AS u ON u.id = ga.userID " .
            "INNER JOIN Appointment AS a ON a.groupID = g.id " .
            "INNER JOIN Time_Slots AS ts ON ts.id = a.timeSlotID " .
            "INNER JOIN User AS instructor ON instructor.id = ts.instructorID " .
            "WHERE u.id = $userID;";
    }

    public function cancelAppointment($appointmentID)
    {
        return "UPDATE Time_Slots AS ts " .
            "JOIN Appointment AS a ON ts.id = a.timeSlotID " .
            "SET ts.isAvailable = 1 " .
            "WHERE a.id = $appointmentID; " .
            "DELETE FROM Appointment WHERE id = $appointmentID;";
    }

    public function scheduleAppointment($timeSlotID, $groupID)
    {
        return "INSERT INTO Appointment (timeSlotID, groupID) " .
            "VALUES ($timeSlotID, $groupID); " .
            "UPDATE Time_Slots AS ts " .
            "JOIN Appointment AS a ON ts.id = a.timeSlotID " .
            "SET ts.isAvailable = 0 " .
            "WHERE ts.id = $timeSlotID;";
    }
}
?>
