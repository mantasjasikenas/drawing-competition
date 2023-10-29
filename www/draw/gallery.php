<?php
global $database, $session;
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
                </div>
                <br>

                <div style="padding: 10px">
                    <?php
                    $result = $database->getTop10Paintings();

                    if ($result && count($result) > 0) {
                        echo '<h2 style="margin-top: unset; text-align: center">Geriausi 10 darbų</h2>';

                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center; gap : 6px">';

                        foreach ($result as $row) {
                            $imageData = $row['image'];
                            $score = $row['score'];
                            echo '<div style="text-align: center;">';
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="height: 150px;">';
//                            echo '<h3 style="margin-top: unset; margin-bottom: unset">' . $score . '</h3>';
                            echo '</div>';
                        }

                        echo '</div>';
                    } else {
                        echo '<h2 style="margin-top: unset; color: red; text-align: center">Nėra pateiktų paveikslėlių</h2>';
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