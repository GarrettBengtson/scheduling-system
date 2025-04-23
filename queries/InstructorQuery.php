<?php

namespace InstructorQuery;

// Include connection to db file
require "connection.php";

class InstructorQuery
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    /*
     * Gets all available (isAvailable = 1) time slots based 
     * on the inputted date.
     * instructorID should be passed in through cookies/the
     * logged in user's id.
    */
    public function getAvailableTimeSlotsByDate($instructorID, $date)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Time_Slots "
            . "WHERE instructorID = ? "
            . "AND isAvailable = 1 "
            . "AND date = ?;");
        $stmt->bind_param("is", $instructorID, $date); // Bind parameters
        $stmt->execute(); // Execute query
        $stmt->store_result(); // Store result for further use
        $timeslots = array();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $iID, $startTime, $endTime, $dateOf, $available);

            // Fetch the results and store them in the array
            while ($stmt->fetch()) {
                $timeslots[] = array(
                    'timeslotId' => $id,
                    'instructorId' => $iID,
                    'startTime' => date("h:i:s A", strtotime($startTime)),
                    'endTime' => date("h:i:s A", strtotime($endTime)),
                    'date' => $dateOf,
                    'isAvailable' => $available
                );
            }
        } else {
            return "No available time slots found for the given instructor and date.";
        }
        $stmt->close();
        return $timeslots;
    }

    public function getAllFutureAvailableTimeSlots($instructorID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Time_Slots "
            . "WHERE instructorID = ? "
            . "AND isAvailable = 1 "
            .  "AND date >= NOW();");
        $stmt->bind_param("i", $instructorID); // Bind parameters
        $stmt->execute(); // Execute query
        $stmt->store_result(); // Store result for further use
        $timeslots = array();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $iID, $startTime, $endTime, $dateOf, $available);

            // Fetch the results and store them in the array
            while ($stmt->fetch()) {
                $timeslots[] = array(
                    'timeslotId' => $id,
                    'instructorId' => $iID,
                    'startTime' => date("h:i:s A", strtotime($startTime)),
                    'endTime' => date("h:i:s A", strtotime($endTime)),
                    'date' => $dateOf,
                    'isAvailable' => $available
                );
            }
        } else {
            return "No available time slots found for the given instructor.";
        }
        $stmt->close();
        return $timeslots;
    }
    //Gets all timeslots that the instructor has made
    public function getAllTimeSlotsOrderedByDate($instructorID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Time_Slots "
            . "WHERE instructorID = ? "
            . "ORDER BY date;");
        $stmt->bind_param("i", $instructorID); // Bind parameters
        $stmt->execute(); // Execute query
        $stmt->store_result(); // Store result for further use
        $timeslots = array();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $iID, $startTime, $endTime, $dateOf, $available);

            // Fetch the results and store them in the array
            while ($stmt->fetch()) {
                $timeslots[] = array(
                    'timeslotId' => $id,
                    'instructorId' => $iID,
                    'startTime' => date("h:i:s A", strtotime($startTime)),
                    'endTime' => date("h:i:s A", strtotime($endTime)),
                    'date' => $dateOf,
                    'isAvailable' => $available
                );
            }
        } else {
            return "No time slots found for the given instructor.";
        }
        $stmt->close();
        return $timeslots;
    }

    /*
     * Gets all scheduled appointments (isAvailable = 0) based
     * on the inputted date. Also shows the group name of the group
     * tied to the appointment. 
     * instructorID should be passed in through cookies/the
     * logged in user's id.
    */
    public function getScheduledAppointmentsByDate($instructorID, $date)
    {
        $stmt = $this->conn->prepare("SELECT g.id as groupID, g.projectName, ts.startTime, ts.endTime, ts.date, a.id AS appointmentID"
            . " FROM Time_Slots AS ts"
            . " INNER JOIN Appointment AS a"
            . " ON a.timeSlotID = ts.id"
            . " INNER JOIN `Group` as g"
            . " ON g.id = a.groupID"
            . " WHERE ts.instructorID = ?"
            . " AND date = ?;");
        $stmt->bind_param("is", $instructorID, $date); // Bind parameters
        $stmt->execute(); // Execute query
        $stmt->store_result(); // Store result for further use
        $appointments = array();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($groupID, $projectName, $startTime, $endTime, $date, $appointmentID);

            // Fetch the results and store them in the array
            while ($stmt->fetch()) {
                $appointments[] = array(
                    'groupID' => $groupID,
                    'projectName' => $projectName,
                    'startTime' => date("h:i:s A", strtotime($startTime)),
                    'endTime' => date("h:i:s A", strtotime($endTime)),
                    'date' => $date,
                    'appointmentID' => $appointmentID
                );
            }
        } else {
            return "No available time slots found for the given instructor and date.";
        }
        $stmt->close();
        return $appointments;
    }

    /*
     * Gets all appointments occurring after the current moment.
     * instructorID should be passed in through cookies/the
     * logged in user's id.
    */
    public function getAllFutureScheduledAppointments($instructorID)
    {
        $stmt = $this->conn->prepare("SELECT g.id as groupID, g.projectName, ts.startTime, ts.endTime, ts.date, a.id AS appointmentID"
            . " FROM Time_Slots AS ts"
            . " INNER JOIN Appointment AS a"
            . " ON a.timeSlotID = ts.id"
            . " INNER JOIN `Group` as g"
            . " ON g.id = a.groupID"
            . " WHERE ts.instructorID = ?"
            .  " AND date >= NOW();");
        $stmt->bind_param("i", $instructorID); // Bind parameters
        $stmt->execute(); // Execute query
        $stmt->store_result(); // Store result for further use
        $appointments = array();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($groupID, $projectName, $startTime, $endTime, $date, $appointmentID);

            // Fetch the results and store them in the array
            while ($stmt->fetch()) {
                $appointments[] = array(
                    'groupID' => $groupID,
                    'projectName' => $projectName,
                    'startTime' => date("h:i:s A", strtotime($startTime)),
                    'endTime' => date("h:i:s A", strtotime($endTime)),
                    'date' => $date,
                    'appointmentID' => $appointmentID
                );
            }
        } else {
            return "No available time slots found for the given instructor.";
        }
        $stmt->close();
        return $appointments;
    }

    /*
     * Deletes the selected time slot entry and any
     * appointment connected to it.
    */
    public function deleteTimeSlot($timeSlotID)
    {
        // Delete associated appointments
        $stmt1 = $this->conn->prepare("DELETE FROM Appointment WHERE timeSlotID = ?");
        $stmt1->bind_param("i", $timeSlotID); // Bind parameters
        if ($stmt1->execute()) { // Execute query
            $stmt1->close();

            // Delete the time slot
            $stmt2 = $this->conn->prepare("DELETE FROM Time_Slots WHERE id = ?");
            $stmt2->bind_param("i", $timeSlotID); // Bind parameters
            if ($stmt2->execute()) { // Execute query
                $stmt2->close();
                return "Time slot and associated appointments deleted successfully.";
            } else {
                $stmt2->close();
                return "Failed to delete time slot.";
            }
        } else {
            $stmt1->close();
            return "Failed to delete associated appointments.";
        }
    }



    /*
     * Deletes the selected appointment entry and sets
     * isAvailable of the correlating Time_Slot entry
     * to 1. Useful if a student tells the professor
     * they can't make it to an appointment.
    */
    public function cancelGroupAppointment($appointmentID)
    {
        // Update the time slot to be available
        $stmt1 = $this->conn->prepare("UPDATE Time_Slots AS ts
                                       JOIN Appointment AS a ON ts.id = a.timeSlotID
                                       SET ts.isAvailable = 1
                                       WHERE a.id = ?");
        $stmt1->bind_param("i", $appointmentID); // Bind parameters
        if ($stmt1->execute()) { // Execute query
            $stmt1->close();

            // Delete the appointment
            $stmt2 = $this->conn->prepare("DELETE FROM Appointment WHERE id = ?");
            $stmt2->bind_param("i", $appointmentID); // Bind parameters
            if ($stmt2->execute()) { // Execute query
                $stmt2->close();
                return "Appointment canceled and time slot updated successfully.";
            } else {
                $stmt2->close();
                return "Failed to delete appointment.";
            }
        } else {
            $stmt1->close();
            return "Failed to update time slot.";
        }
    }


    public function getAllGroups()
    {
        $stmt = $this->conn->prepare("SELECT id, projectName, groupLeaderID FROM `Group`;");
        $stmt->execute(); // Execute query
        $stmt->store_result(); // Store result for further use
        $groups = array();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $projectName, $groupLeaderID);

            // Fetch the results and store them in the array
            while ($stmt->fetch()) {
                $groups[] = array(
                    'id' => $id,
                    'projectName' => $projectName,
                    'groupLeaderID' => $groupLeaderID
                );
            }
        } else {
            return "No groups found.";
        }
        $stmt->close();
        return $groups;
    }

    /*
     * Creates a new entry in the Appointment table. Input
     * needs to be gathered for Group id, Time_Slot id
     * by selecting from existing groups and time slots.
     * Set isAvailable of selected Time_Slot entry to 0.
    */
    public function setupAppointment($timeSlotID, $groupID)
    {
        // Insert the appointment
        $stmt1 = $this->conn->prepare("INSERT INTO Appointment (timeSlotID, groupID) VALUES (?, ?);");
        $stmt1->bind_param("ii", $timeSlotID, $groupID); // Bind parameters
        if ($stmt1->execute()) { // Execute query
            $stmt1->close();

            // Update the time slot to be unavailable
            $stmt2 = $this->conn->prepare("UPDATE Time_Slots SET isAvailable = 0 WHERE id = ?");
            $stmt2->bind_param("i", $timeSlotID); // Bind parameters
            if ($stmt2->execute()) { // Execute query
                $stmt2->close();
                return "Appointment set up successfully and time slot updated.";
            } else {
                $stmt2->close();
                return "Failed to update time slot.";
            }
        } else {
            $stmt1->close();
            return "Failed to set up appointment.";
        }
    }


    /*
     * Creates a new Time_Slot entry. Input validation needs
     * to be handled in the form for getting the times and date.
     * HH:MM:SS format for times. YYYY-MM-DD for date.
     * Pass in the instructorID using cookies of the logged in
     * user. isAvailable is set to 1 because the Time Slot will 
     * inherently be available.
    */
    public function createTimeSlot($instructorID, $startTime, $endTime, $date)
    {
        $stmt = $this->conn->prepare("INSERT INTO Time_Slots "
            . "(instructorID, startTime, endTime, date, isAvailable) "
            . "VALUES (?, ?, ?, ?, 1);");
        $stmt->bind_param("isss", $instructorID, $startTime, $endTime, $date); // Bind parameters
        if ($stmt->execute()) { // Execute query
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
    /*
      * List group information based on the inputted groupID.
     */
    public function getGroupInfo($groupID)
    {
        $stmt = $this->conn->prepare("SELECT u.username, u.email, g.projectName, IF(u.id = g.groupLeaderID, 'True', 'False') AS isGroupLeader
                                      FROM `Group` AS g
                                      INNER JOIN Group_Association AS ga ON g.id = ga.groupID
                                      INNER JOIN User AS u ON u.id = ga.userID
                                      WHERE g.id = ?");
        $stmt->bind_param("i", $groupID); // Bind parameters
        $stmt->execute(); // Execute query
        $stmt->store_result(); // Store result for further use
        $groupInfo = array();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($username, $email, $projectName, $isGroupLeader);

            // Fetch the results and store them in the array
            while ($stmt->fetch()) {
                $groupInfo[] = array(
                    'username' => $username,
                    'email' => $email,
                    'projectName' => $projectName,
                    'isGroupLeader' => $isGroupLeader
                );
            }
        } else {
            return "No group information found for the given group ID.";
        }
        $stmt->close();
        return $groupInfo;
    }
}
