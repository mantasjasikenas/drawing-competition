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
        } else if ($_GET['rp']) {
            $this->removeCompPaintings();
        } else if ($_GET['rc']) {
            $this->removeCompetition();
        } else if ($_GET['delreport']) {
            $this->deleteReport();

        } else if ($_GET['delpaint']) {
            $this->deletePainting();
        } else {
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
        global $database, $session, $form;

        $topic = $_POST['topic'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $img = $_FILES['img']['tmp_name'];

        if (!$img) {
            $form->setError("img", "* Nepasirinktas paveikslėlis<br>");
        }

        if (!$topic) {
            $form->setError("topic", "* Nepasirinkta konkurso tema<br>");
        }

        if (!$start_date) {
            $form->setError("start_date", "* Nepasirinkta konkurso pradžios data<br>");
        }

        if (!$end_date) {
            $form->setError("end_date", "* Nepasirinkta konkurso pabaigos data<br>");
        }

        if ($start_date > $end_date) {
            $form->setError("end_date", "* Konkurso pabaigos data negali būti ankstesnė nei pradžios data<br>");
        }

        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        } else {
            $img_content = file_get_contents($img);
            $res = $database->createCompetition($topic, $start_date, $end_date, $img_content);

            if (!$res) {
                $form->setError("topic", "* Nepavyko sukurti konkurso<br>");
            }
        }


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

    function removeCompPaintings()
    {
        global $session, $database, $form;
        $comp_id = $this->checkCompetitionId("delcomppaint");

        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        } else {
            $q = "DELETE FROM uploads WHERE fk_competition = '$comp_id'";
            $res = $database->query($q);

            if (!$res) {
                $form->setError("delcomppaint", "* Nepavyko ištrinti paveikslėlių<br>");
            }

        }
        header("Location: " . $session->referrer);
    }

    function removeCompetition()
    {
        global $session, $database, $form;
        $comp_id = $this->checkCompetitionId("delcomp");

        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        } else {
            $q = "DELETE FROM competitions WHERE id = '$comp_id'";
            $res = $database->query($q);

            if (!$res) {
                $form->setError("delcomp", "* Nepavyko ištrinti konkurso<br>");
            }

        }
        header("Location: " . $session->referrer);
    }

    function checkCompetitionId($uname): string
    {
        global $database, $form;


        $id = $_REQUEST[$uname];
        $field = $uname;

        $id = stripslashes($id);

        if (!$database->query("SELECT * FROM competitions WHERE id = '$id'")) {
            $form->setError($field, "* Nepavyko rasti nurodyto konkurso<br>");
        }


        return $id;
    }

    function deleteReport()
    {
        global $session, $database, $form;

        $id = $_REQUEST['id'];

        $id = stripslashes($id);

        if (!$database->query("DELETE FROM reports WHERE id = '$id'")) {
            $form->setError("global", "* Nepavyko ištrinti pranešimo<br>");
        }

        header("Location: " . $session->referrer);
    }

    function deletePainting()
    {
        global $session, $database, $form;

        $id = $_REQUEST['id'];
        $id = stripslashes($id);

        if (!$database->query("DELETE FROM paintings WHERE id = '$id'")) {
            $form->setError("global", "* Nepavyko ištrinti paveikslėlio<br>");
        }

        header("Location: " . $session->referrer);
    }


}

/* Initialize process */
$adminprocess = new AdminProcess;