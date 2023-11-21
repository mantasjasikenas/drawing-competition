<?php
global $database, $session;
include("include/session.php");
if ($session->logged_in && ($session->isParticipant() || $session->isEvaluator())
) {
    ?>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Įkelti paveikslėlį</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="include/style.php" media="screen">
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

                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center; gap : 12px">';

                        foreach ($result as $row) {
                            $imageData = $row['image'];
                            $score = $row['score'];
                            $style = $row['style'];
                            $username = $row['username'];

                            if ($score) {
                                $score = round($score, 2);
                            } else {
                                $score = "-";
                            }


                            echo '<div style="text-align: center; border: solid 2px black; border-radius: 5px; padding: 5px; background-color: #c3fdb8;">';
                            echo '<img class="' . $style . '" src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="height: 150px;">';

                            echo '<h4 style="margin-top: unset; margin-bottom: unset">Įvertinimas</h4>';
                            echo '<label style="margin-top: unset; margin-bottom: unset">' . $score . '</label>';

                            echo '<h4 style="margin-top: unset; margin-bottom: unset">Autorius</h4>';
                            echo '<label style="margin-top: unset; margin-bottom: unset">' . $username . '</label>';

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