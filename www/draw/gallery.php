<?php
global $database;
include("include/session.php");
if ($session->logged_in) {
    ?>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Įkelti paveikslėlį</title>
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
                    <h1>Galerija</h1>
                    <?php
                    $topic = $database->getCurrentCompetition()['topic'];
                    echo '<h2 style="margin-bottom: unset">Konkurso tema</h2>';
                    echo '<h3 style="margin-top: unset; color: rgba(50,194,137,0.62)">' . $topic . '</h3>';
                    ?>

                </div>
                <br>

                <div style="padding: 10px">
                    <?php
                    $dir = "uploads/";
                    $images = glob($dir . "*.{jpg,png,gif,svg}", GLOB_BRACE);

                    if (count($images) == 0) {
                        echo '<h2 style="margin-top: unset; color: red; text-align: center">Nėra pateiktų paveikslėlių</h2>';
                    } else {
                        echo '<h2 style="margin-top: unset; text-align: center">Geriausi 10 darbų</h2>';

                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center">';

                        for ($i = 0; $i < 10; $i++) {
                            echo '<img height="200" src="' . $images[$i] . '" /><br />';
                        }

                        echo '</div>';
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