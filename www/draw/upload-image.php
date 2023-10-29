<?php
global $session, $database;
include("include/session.php");


if ($session->logged_in) {
    ?>
    <html lang="lt">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Įkelti paveikslėlį</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css"/>
        <style>
            .oval {
                width: 320px;
                height: 160px;
                background: #a84909;
                border-radius: 50%;
            }

            .border {
                border: 4px solid rgba(50, 194, 137, 0.62);
            }

            .rounded {
                border-radius: 10%;
            }
        </style>
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
                include("include/meniu.php");
                ?>
                <table style="border-width: 2px; border-style: dotted;">
                    <tr>
                        <td>
                            Atgal į [<a href="index.php">Pradžia</a>]
                        </td>
                    </tr>
                </table>
                <br>
                <div style="text-align: center;">
                    <h1>Įkelti paveikslėlį</h1>
                    <?php

                    if (isset($_SESSION['message'])) {
                        echo '<h3 style="color: red">' . $_SESSION['message'] . '</h3>';
                        unset($_SESSION['message']);
                    }

                    ?>

                </div>
                <br>

                <div style="padding: 10px; text-align: center">

                    <form enctype="multipart/form-data" method="post" action="actions/upload.php">
                        <?php
                        $competitions = $database->getAvailableCompetitions();

                        if (count($competitions) > 0) {
                            echo '<label for="competition">Pasirinkite konkursą</label><br>';
                            echo '<select name="competition" id="competition">';
                            foreach ($competitions as $competition) {
                                echo '<option value="' . $competition['id'] . '">' . $competition['topic'] . ' ' . $competition['start_date'] . ' - ' . $competition['end_date'] . '</option>';
                            }
                            echo '</select><br><br>';
                        }


                        echo '<label for="style">Pasirinkite paveikslėlių stilių</label><br>';
                        echo '<select name="style" id="style">';
                        echo '<option value="default">Numatytasis</option>';
                        echo '<option value="border">Rėmelis</option>';
                        echo '<option value="rounded">Apvalūs kampai</option>';
                        echo '<option value="oval">Ovalas</option>';
                        echo '</select><br><br>';


                        ?>

                        <label for="files">Pasirinkite piešinių nuotraukas</label><br>
                        <input name="images[]" multiple type="file" id="file[]" accept="image/*"
                               required oninvalid="this.setCustomValidity('Privaloti pateikti bent vieną piešinį.')"
                               onchange="this.setCustomValidity('')"><br><br>
                        <textarea required name="comment" placeholder="Komentaras"
                                  cols="40" rows="5"></textarea
                        >
                        <br><br>
                        <input type="submit" value="Pateikti">
                    </form>

                    <br><br>

                    <?php
                    if (isset($_SESSION['upload_id'])) {
                        $images = $database->getUploadImages($_SESSION['upload_id']);
                        $styleClass = $_SESSION['style'];

                        echo '<h3 style="margin-bottom: unset">Įkelti paveikslėliai</h3>';
                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center">';

                        foreach ($images as $image) {
                            echo '<div style="margin: 10px; width: 320px">';
                            echo '<img class="' . $styleClass . '"  style="width: 100%; height: 100%; object-fit: cover" src="data:image/jpeg;base64,' . base64_encode($image['image']) . '"/>';
                            echo '</div>';
                        }

                        echo '</div>';

                        unset($_SESSION['upload_id']);
                        unset($_SESSION['style']);
                    }


                    ?>
                </div>

        <tr>
            <td>
                <?php
                include("include/footer.php");
                ?>
            </td>
        </tr>
    </table>
    </body>
    </html>
    <?php
    //Jei vartotojas neprisijungęs, užkraunamas pradinis puslapis  
} else {
    header("Location: index.php");
}
?>