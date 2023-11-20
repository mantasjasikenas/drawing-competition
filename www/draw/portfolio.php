<?php
global $database, $session;
include("include/session.php");
if ($session->logged_in && $session->isParticipant()
) {
    ?>
    <html lang="lt">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Portfolio</title>
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
                    <h1>Portfolio</h1>
                </div>
                <br>

                <div style="padding: 10px">
                    <?php
                    $result = $database->getUserPaintingsAndScores($session->id);

                    if ($result && count($result) > 0) {
                        echo '<h2 style="margin-top: unset; text-align: center">Jūsų pateikti darbai ir jų įvertinimai</h2>';

                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center; gap : 10px">';

                        foreach ($result as $row) {
                            $imageData = $row['image'];
                            $score = $row['score'];
                            $style = $row['style'];

                            $composition = $row['composition'];
                            $colorfulness = $row['colorfulness'];
                            $compliance = $row['compliance'];
                            $originality = $row['originality'];


                            echo '<div style="text-align: center; border: solid 2px black; border-radius: 5px; padding: 5px; background-color: #c3fdb8;">';

                            echo '<img class="' . $style . '" src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="height: 200px; width: auto">';

                            if (!$score || $score == 0) {
                                $score = '-';
                            } else {
                                $score = number_format($score, 2, '.', '');
                                $composition = number_format($composition, 2, '.', '');
                                $colorfulness = number_format($colorfulness, 2, '.', '');
                                $compliance = number_format($compliance, 2, '.', '');
                                $originality = number_format($originality, 2, '.', '');
                            }

                            echo '<h4 style="margin-top: unset; margin-bottom: unset">' . 'Bendras įvertinimas' . '</h4>';
                            echo '<h5 style="margin-top: unset; margin-bottom: unset">' . $score . '</h5>';

                            echo '<h4 style="margin-top: unset; margin-bottom: unset">' . 'Kompozicija' . '</h4>';
                            echo '<h5 style="margin-top: unset; margin-bottom: unset">' . $composition . '</h5>';

                            echo '<h4 style="margin-top: unset; margin-bottom: unset">' . 'Spalvingumas' . '</h4>';
                            echo '<h5 style="margin-top: unset; margin-bottom: unset">' . $colorfulness . '</h5>';

                            echo '<h4 style="margin-top: unset; margin-bottom: unset">' . 'Atitikimas temai' . '</h4>';
                            echo '<h5 style="margin-top: unset; margin-bottom: unset">' . $compliance . '</h5>';

                            echo '<h4 style="margin-top: unset; margin-bottom: unset">' . 'Originalumas' . '</h4>';
                            echo '<h5 style="margin-top: unset; margin-bottom: unset">' . $originality . '</h5>';


                            echo '</div>';
                        }

                        echo '</div>';
                    } else {
                        echo '<h2 style="margin-top: unset; color: red; text-align: center">Nėra pateiktų darbų!</h2>';
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