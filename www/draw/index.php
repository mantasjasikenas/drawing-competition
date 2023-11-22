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
                    <h1>Pagrindinis puslapis</h1>

                    <div style="padding: 5px 60px; background-color: #c3fdb8; width: fit-content; margin: auto; border: solid 2px black; border-radius: 5px;">
                        <h3 style="margin-bottom: 0">Darbo pavadinimas</h3>
                        <p style="margin-top: 6px">Piešinių konkursas</p>

                        <h3 style="margin-bottom: 0">Autorius</h3>
                        <p style="margin-top: 6px">Mantas Jasikėnas IFF-1/4</p>
                    </div>


                    <?php

                    /*                    $result = $database->getCurrentCompetitionPaintings();

                                        if ($result && count($result) > 0) {

                                            echo '<div style="display: flex; flex-wrap: wrap; justify-content: center">';

                                            foreach ($result as $row) {
                                                $imageData = $row['image'];
                                                echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="height: 150px;">';
                                            }

                                            echo '</div>';


                                        }*/

                    ?>


                </div><br>
                <?php
                //Jei vartotojas neprisijungęs, rodoma prisijungimo forma
                //Jei atsiranda klaidų, rodomi pranešimai.
            } else {
                echo "<div align=\"center\">";

                if ($form->num_errors > 0) {
                    echo '<div class="error-msg">
                            <i class="fa fa-check"></i>' . 'Įvyko klaida!' . '
                         </div>';
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