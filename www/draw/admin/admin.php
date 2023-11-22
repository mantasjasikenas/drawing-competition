<?php
global $session, $form;
include("../include/session.php");

//Iš pradžių aprašomos funkcijos, po to jos naudojamos.

/**
 * displayUsers - Displays the users database table in
 * a nicely formatted html table.
 */
function displayUsers()
{
    global $database;
    $q = "SELECT username,userlevel,email,timestamp " . "FROM " . TBL_USERS . " ORDER BY userlevel DESC,username";
    $result = $database->query($q);
    /* Error occurred, return given name by default */
    $num_rows = mysqli_num_rows($result);
    if (!$result || ($num_rows < 0)) {
        echo "Error displaying info";
        return;
    }
    if ($num_rows == 0) {
        echo "Lentelė tuščia.";
        return;
    }
    /* Display table contents */
    echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
    echo "<tr><td><b>Vartotojo vardas</b></td><td><b>Lygis</b></td><td><b>E-paštas</b></td><td><b>Paskutinį kartą aktyvus</b></td><td><b>Veiksmai</b></td></tr>\n";
    for ($i = 0; $i < $num_rows; $i++) {
        $uid = $uname = mysqli_result($result, $i, "username");
        $ulevel = mysqli_result($result, $i, "userlevel");
        $ulevelname = '';
        switch ($ulevel) {
            case ADMIN_LEVEL:
                $ulevelname = ADMIN_NAME;
                break;
            case EVALUATOR_LEVEL:
                $ulevelname = EVALUATOR_NAME;
                break;
            case USER_LEVEL:
                $ulevelname = USER_NAME;
                break;
            default :
                $ulevelname = 'Neegzistuojantis tipas';
        }
        //Europe/Vilnius
        $email = mysqli_result($result, $i, "email");
        $time = date("Y-m-d G:i", mysqli_result($result, $i, "timestamp"));
        $ulevelchange = '<form action="adminprocess.php" method="POST">
                        
                                <input type="hidden" name="upduser" value="' . $uname . '">
                                <input type="hidden" name="subupdlevel" value="1">
                                <select name="updlevel" onChange="alert(\'Pakeistas vartotojo lygis!\');submit();">
                                    <option value="' . USER_LEVEL . '" ' . ($ulevel == USER_LEVEL ? 'selected' : '') . '>' . USER_NAME . '</option>
                                    <option value="' . EVALUATOR_LEVEL . '" ' . ($ulevel == EVALUATOR_LEVEL ? 'selected' : '') . '>' . EVALUATOR_NAME . '</option>
                                    <option value="' . ADMIN_LEVEL . '" ' . ($ulevel == ADMIN_LEVEL ? 'selected' : '') . '>' . ADMIN_NAME . '</option>
                                </select>
                                

                    </form>';
        echo "<tr><td>$uname</td><td>$ulevelchange</td><td>$email</td><td>$time</td><td><a href='adminprocess.php?b=1&banuser=$uname' onclick='return confirm(\"Ar tikrai norite blokuoti?\");'>Blokuoti</a> | <a href='adminprocess.php?d=1&deluser=$uname' onclick='return confirm(\"Ar tikrai norite trinti?\");'>Trinti</a></td></tr>\n";
    }
    echo "</table><br>\n";
}

function displayCompetitions()
{
    global $database;
    $q = "SELECT competitions.id,
                competitions.image,
               competitions.topic,
               competitions.start_date,
               competitions.end_date,
               competitions.creation_date,
               COUNT(paintings.id) AS submissions
        FROM paintings
                 LEFT JOIN uploads ON paintings.fk_upload = uploads.id
                 RIGHT JOIN competitions ON uploads.fk_competition = competitions.id
        GROUP BY competitions.id";

    $result = $database->query($q);

    $num_rows = mysqli_num_rows($result);

    if (!$result || ($num_rows < 0)) {
        echo "Error displaying info";
        return;
    }

    if ($num_rows == 0) {
        echo "Lentelė tuščia.";
        return;
    }
    /* Display table contents */
    echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";

    echo "<tr>
            <td><b>Id</b></td>
            <td><b>Nuotrauka</b></td>
            <td><b>Tema</b></td>
            <td><b>Sukūrimo data</b></td>
            <td><b>Pradžios data</b></td>
            <td><b>Pabaigos data</b></td>
            <td><b>Pateiktų piešinių skaičius</b></td>
            <td><b>Veiksmai</b></td>
        </tr>\n";

    for ($i = 0; $i < $num_rows; $i++) {
        $id = mysqli_result($result, $i, "id");
        $image = mysqli_result($result, $i, "image");
        $topic = mysqli_result($result, $i, "topic");
        $start_date = mysqli_result($result, $i, "start_date");
        $end_date = mysqli_result($result, $i, "end_date");
        $creation_date = mysqli_result($result, $i, "creation_date");
        $submissions = mysqli_result($result, $i, "submissions");

        echo "<tr>
                <td>$id</td>
                <td><img class='myImg' id='myImg' style='height: 40px; object-fit: cover' src='data:image/jpeg;base64," . base64_encode($image) . "'/></td>
                <td>$topic</td>
                <td>$creation_date</td>
                <td>$start_date</td>
                <td>$end_date</td>
                <td>$submissions</td>
                <td>
                    <a href='adminprocess.php?rp=1&delcomppaint=$id' 
                       onclick='return confirm(\"Ar tikrai norite ištrinti visus konkurso $topic piešinius?\");'>Trinti piešinius</a> 
                    |
                     <a href='adminprocess.php?rc=1&delcomp=$id' 
                       onclick='return confirm(\"Ar tikrai norite ištrinti konkursą $topic?\");'>Trinti konkursą</a> 
               
               </td>

            </tr>\n";
    }
    echo "</table><br>\n";
    echo '<div id="myModal" class="modal">
            <img class="modal-content" id="img01">
          </div>';

    echo '<script>
        const modal = document.getElementById("myModal");
        const modalImg = document.getElementById("img01");
        const captionText = document.getElementById("caption");
        const images = document.querySelectorAll(".myImg");

        images.forEach(img => {
            img.onclick = function () {
                modal.style.display = "block";
                modalImg.src = this.src;
                modalImg.alt = this.alt;
                captionText.innerHTML = this.alt;
            };
        });

        modal.onclick = function () {
            modalImg.className += " out";
            setTimeout(function () {
                modal.style.display = "none";
                modalImg.className = "modal-content";
            }, 400);
        };
        
        window.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                modalImg.className += " out";
                setTimeout(function () {
                    modal.style.display = "none";
                    modalImg.className = "modal-content";
                }, 400);
            }
        })
    </script>';
}

function mysqli_result($res, $row, $field = 0)
{
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}

/**
 * displayBannedUsers - Displays the banned users
 * database table in a nicely formatted html table.
 */
function displayBannedUsers()
{
    global $database;
    $q = "SELECT username,timestamp " . "FROM " . TBL_BANNED_USERS . " ORDER BY username";
    $result = $database->query($q);
    /* Error occurred, return given name by default */
    $num_rows = mysqli_num_rows($result);
    if (!$result || ($num_rows < 0)) {
        echo "Error displaying info";
        return;
    }
    if ($num_rows == 0) {
        echo "Lentelė tuščia.";
        return;
    }
    /* Display table contents */
    echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
    echo "<tr><td><b>Vartotojo vardas</b></td><td><b>Blokavimo laikas</b></td><td><b>Veiksmai</b></td></tr>\n";
    for ($i = 0; $i < $num_rows; $i++) {
        $uname = mysqli_result($result, $i, "username");
        $time = date("Y-m-d G:i", mysqli_result($result, $i, "timestamp"));
        echo "<tr><td>$uname</td><td>$time</td><td><a href='adminprocess.php?db=1&delbanuser=$uname' onclick='return confirm(\"Ar tikrai norite Šalinti?\");'>Šalinti</a></td></tr>\n";
    }
    echo "</table><br>\n";
}


function createNewDrawingCompetition()
{
    global $form;

    echo '<form enctype="multipart/form-data" action="adminprocess.php" method="POST">
            <input type="hidden" name="create_comp">
           
            <label for="name">Konkurso tema:</label><br>
            <input type="text" id="name" name="topic" value="'
        . $form->value("topic") . '"><br>' . $form->error("topic") . '
            <br>
            
            <label for="start_date">Pradžios data:</label><br>
            <input type="date" id="start_date" name="start_date" value="'
        . $form->value("start_date") . '"><br>' . $form->error("start_date") . '
            <br>
            
            <label for="end_date">Pabaigos data:</label><br>
            <input type="date" id="end_date" name="end_date" value="'
        . $form->value("end_date") . '"><br>' . $form->error("end_date") . '
            <br>
            
            <label for="img">Pasirinkite paveikslėlį:</label><br>
            <input type="file" id="img" name="img" accept="image/*"><br>' . $form->error("img") . '
            <br><br>
            
            <input type="submit" value="Sukurti konkursą">
        </form>';

}

function ViewActiveUsers()
{
    global $database;
    if (!defined('TBL_ACTIVE_USERS')) {
        die("");
    }
    $q = "SELECT username FROM " . TBL_ACTIVE_USERS . " ORDER BY timestamp DESC,username";
    $result = $database->query($q);
    /* Error occurred, return given name by default */
    $num_rows = mysqli_num_rows($result);
    if (!$result || ($num_rows < 0)) {
        echo "Error displaying info";
    } else if ($num_rows > 0) {
        /* Display active users, with link to their info */
        echo "<br><table border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
        echo "<tr><td><b>Vartotojų vardai</b></td></tr>";
        echo "<tr><td><font size=\"2\">\n";
        for ($i = 0; $i < $num_rows; $i++) {
            $uname = mysqli_result($result, $i, "username");
            if ($i > 0) echo ", ";
            echo "<a href=\"../userinfo.php?user=$uname\">$uname</a>";
        }
        echo ".";
        echo "</font></td></tr></table>";
    }
}

if (!$session->isAdmin()) {
    header("Location: ../index.php");
} else { //Jei administratorius
    date_default_timezone_set("Europe/Vilnius");
    ?>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Administratoriaus sąsaja</title>
        <link href="../include/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
    <table class="center">
        <tr>
            <td>
                <?php
                include('../components/header.html');
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                $_SESSION['path'] = '../';
                include("../include/meniu.php");
                //Nuoroda į pradžią
                ?>
                <table style="border-width: 2px; border-style: dotted;">
                    <tr>
                        <td>
                            Atgal į [<a href="../index.php">Pradžia</a>]
                        </td>
                    </tr>
                </table>
                <br>
                <?php
                if ($form->num_errors > 0) {
                    echo "<font size=\"4\" color=\"#ff0000\">" . "!*** Įvyko klaida!</font><br><br>";
                }
                ?>
                <table style=" text-align:left;" border="0" cellspacing="5" cellpadding="5">
                    <tr>
                        <td>
                            <?php
                            /**
                             * Display Competition Table
                             */
                            ?>
                            <h3>Konkursai:</h3>
                            <?php
                            displayCompetitions();
                            ?>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            /**
                             * Display Banned Users Table
                             */
                            ?>
                            <h3>Pridėti naują konkursą:</h3>
                            <?php
                            createNewDrawingCompetition();
                            ?>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            /**
                             * Display Users Table
                             */
                            ?>
                            <h3>Sistemos vartotojai:</h3>
                            <?php
                            displayUsers();
                            ?>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            /**
                             * Display Banned Users Table
                             */
                            ?>
                            <h3>Blokuoti vartotojai:</h3>
                            <?php
                            displayBannedUsers();
                            ?>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <hr>
                        </td>
                    </tr>
            </td>
        </tr>

        <tr>
            <td>
                <h3>Šiuo metu prisijungę vartotojai:</h3>
                <?php
                ViewActiveUsers();
                ?>
        <tr>
            <td>
                <hr>
            </td>
        </tr>
        </td></tr>
        <tr>
            <td>
                <?php
                /**
                 * Delete Inactive Users
                 */
                ?>
                <h3>Šalinti neaktyvius vartotojus</h3>
                <table>
                    <form action="adminprocess.php" method="POST">
                        <tr>
                            <td>
                                Neaktyvumo dienos:<br>
                                <select name="inactdays">
                                    <option value="3">3
                                    <option value="7">7
                                    <option value="14">14
                                    <option value="30">30
                                    <option value="100">100
                                    <option value="365">365
                                </select>
                            </td>
                            <td>
                                <br>
                                <input type="hidden" name="subdelinact" value="1">
                                <input type="submit" value="Šalinti">
                            </td>
                    </form>
                </table>
            </td>
        </tr>

    </table>
    </td></tr>
    <?php
    echo "<tr><td>";
    include("../include/footer.php");
    echo "</td></tr>";
    ?>
    </table>
    </body>
    </html>
    <?php
}
?>