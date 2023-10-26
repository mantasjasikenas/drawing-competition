<?php

include("../include/session.php");

class AdminProcess
{
    /* Class constructor */

    function AdminProcess()
    {
        global $session;
        /* Make sure administrator is accessing page */
        if (!$session->isAdmin()) {
            header("Location: ../index.php");
            return;
        }
        /* Admin submitted update user level form */
        if (isset($_POST['subupdlevel'])) {
            $this->procUpdateLevel();
        } /* Admin submitted delete user form */ else if (isset($_GET['d'])) {
            $this->procDeleteUser();
        } /* Admin submitted delete inactive users form */ else if (isset($_POST['subdelinact'])) {
            $this->procDeleteInactive();
        } /* Admin submitted ban user form */ else if (isset($_GET['b'])) {
            $this->procBanUser();
        } /* Admin submitted delete banned user form */ else if (isset($_GET['db'])) {
            $this->procDeleteBannedUser();
        } else if (isset($_POST['create_comp'])) {
            $this->procCreateComp();
        } /* Should not get here, redirect to home page */ else {
            header("Location: ../index.php");
        }
    }

    /**
     * procUpdateLevel - If the submitted username is correct,
     * their user level is updated according to the admin's
     * request.
     */
    function procUpdateLevel()
    {
        global $session, $database, $form;
        /* Username error checking */
        $subuser = $this->checkUsername("upduser");

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } /* Update user level */ else {
            $database->updateUserField($subuser, "userlevel", (int)$_POST['updlevel']);
            header("Location: " . $session->referrer);
        }
    }

    /**
     * procDeleteUser - If the submitted username is correct,
     * the user is deleted from the database.
     */
    function procDeleteUser()
    {
        global $session, $database, $form;
        /* Username error checking */
        $subuser = $this->checkUsername("deluser");
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } /* Delete user from database */ else {
            $q = "DELETE FROM " . TBL_USERS . " WHERE username = '$subuser'";
            $database->query($q);
            header("Location: " . $session->referrer);
        }
    }

    /**
     * procDeleteInactive - All inactive users are deleted from
     * the database, not including administrators. Inactivity
     * is defined by the number of days specified that have
     * gone by that the user has not logged in.
     */
    function procDeleteInactive()
    {
        global $session, $database;
        $inact_time = $session->time - $_POST['inactdays'] * 24 * 60 * 60;
        $q = "DELETE FROM " . TBL_USERS . " WHERE timestamp < $inact_time "
            . "AND userlevel != " . ADMIN_LEVEL;
        $database->query($q);
        header("Location: " . $session->referrer);
    }

    /**
     * procBanUser - If the submitted username is correct,
     * the user is banned from the member system, which entails
     * removing the username from the users table and adding
     * it to the banned users table.
     */
    function procBanUser()
    {
        global $session, $database, $form;
        /* Username error checking */
        $subuser = $this->checkUsername("banuser");

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } /* Ban user from member system */ else {
            $q = "DELETE FROM " . TBL_USERS . " WHERE username = '$subuser'";
            $database->query($q);

            $q = "INSERT INTO " . TBL_BANNED_USERS . " VALUES ('$subuser', $session->time)";
            $database->query($q);
            header("Location: " . $session->referrer);
        }
    }

    /**
     * procDeleteBannedUser - If the submitted username is correct,
     * the user is deleted from the banned users table, which
     * enables someone to register with that username again.
     */
    function procDeleteBannedUser()
    {
        global $session, $database, $form;
        /* Username error checking */
        $subuser = $this->checkUsername("delbanuser", true);

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        } /* Delete user from database */ else {
            $q = "DELETE FROM " . TBL_BANNED_USERS . " WHERE username = '$subuser'";
            $database->query($q);
            header("Location: " . $session->referrer);
        }
    }

    function procCreateComp()
    {
        global $session;
        $topic = $_POST['topic'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        global $database, $form;

        $database->addNewCompetion($topic, $start_date, $end_date);

        header("Location: " . $session->referrer);
    }

    /**
     * checkUsername - Helper function for the above processing,
     * it makes sure the submitted username is valid, if not,
     * it adds the appropritate error to the form.
     */
    function checkUsername($uname, $ban = false)
    {
        global $database, $form;
        /* Username error checking */
        $subuser = $_REQUEST[$uname];
        $field = $uname;  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Username not entered<br>");
        } else {
            /* Make sure username is in database */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 5 || strlen($subuser) > 30 ||
                //     !eregi("^([0-9a-z])+$", $subuser) ||
                (!$ban && !$database->usernameTaken($subuser))) {
                $form->setError($field, "* Username does not exist<br>");
            }
        }
        return $subuser;
    }

}

/* Initialize process */
$adminprocess = new AdminProcess;