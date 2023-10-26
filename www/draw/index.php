<?php
global $form, $session, $database;
include("include/session.php");
?>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
    <title>Piešinių konkursas</title>
    <link href="include/styles.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<table class="center">
    <tr>
        <td>
            <?php
            include('components/header.html');
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php
            //Jei vartotojas prisijungęs
            if ($session->logged_in) {
                include("include/meniu.php");
                ?>
                <div style="text-align: center;">
                    <br><br>
                    <h1>Nuotraukos dalyvaujančios konkurse</h1>


                    <?php
                    $topic = $database->getCurrentCompetition()['topic'];
                    echo '<h2 style="margin-bottom: unset">Konkurso tema</h2>';
                    echo '<h3 style="margin-top: unset; color: rgba(50,194,137,0.62)">' . $topic . '</h3>';


                    $result = $database->getCurrentCompetitionPaintings();

                    if ($result && count($result) > 0) {

                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center">';

                        foreach ($result as $row) {
                            $imageData = $row['image'];
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="height: 150px;">';
                        }

                        echo '</div>';


                    }

                    ?>


                </div><br>
                <?php
                //Jei vartotojas neprisijungęs, rodoma prisijungimo forma
                //Jei atsiranda klaidų, rodomi pranešimai.
            } else {
                echo "<div align=\"center\">";
                if ($form->num_errors > 0) {
                    echo "<font size=\"3\" color=\"#ff0000\">Klaidų: " . $form->num_errors . "</font>";
                }
                echo "<table class=\"center\"><tr><td>";
                include("include/loginForm.php");
                echo "</td></tr></table></div><br></td></tr>";
            }
            echo "<tr><td>";
            include("include/footer.php");
            echo "</td></tr>";
            ?>
        </td>
    </tr>
</table>
</body>
</html>