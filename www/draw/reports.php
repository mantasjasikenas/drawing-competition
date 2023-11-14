<?php
global $database, $session, $form;
include("include/session.php");
if ($session->logged_in) {
    ?>
    <html lang="lt">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Pranešimai</title>
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
                    <h1>Pranešimai apie netinkamus piešinius</h1>

                    <?php
                    echo '<div style="text-align: center;">';
                    echo '<h2 style="margin-top: unset; color: green;">' . $form->error("global") . '</h2>';
                    echo '</div>';
                    ?>
                </div>
                <br>

                <div style="padding: 10px; display: flex; justify-content: center">
                    <?php
                    $result = $database->getUnsolvedReports();


                    if ($result && count($result) > 0) {
                        /* Display table contents */
                        echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";

                        echo "<tr>
                                <td><b>Id</b></td>
                                <td><b>Nuotrauka</b></td>
                                <td><b>Priežastis</b></td>
                                <td><b>Vartototojo vardas</b></td>
                                <td><b>Veiksmai</b></td>
                            </tr>\n";

                        foreach ($result as $row) {
                            $image = $row['image'];
                            $report_id = $row['report_id'];
                            $painting_id = $row['painting_id'];
                            $cause = $row['cause'];
                            $user_id = $row['user_id'];
                            $username = $row['username'];

                            if (!$cause) {
                                $cause = "Nenurodyta";
                            }

                            echo "<tr>
                                    <td>$report_id</td>
                                    <td><img style='height: 40px; object-fit: cover' src='data:image/jpeg;base64," . base64_encode($image) . "'/></td>
                                    <td>$cause</td>
                                    <td>$username</td>
                                    <td>
                                        <a href='admin/adminprocess.php?delreport=1&id=$report_id' 
                                           onclick='return confirm(\"Ar tikrai norite ištrinti pranešimą?\");'>Trinti pranešimą</a> 
                                        |
                                         <a href='admin/adminprocess.php?delpaint=1&id=$painting_id' 
                                           onclick='return confirm(\"Ar tikrai norite ištrinti paveikslėlį?\");'>Trinti paveikslėlį</a>
                                      </td>

                                    </tr>\n";
                        }
                        echo "</table><br>\n";
                    } else {
                        echo '<h2 style="margin-top: unset; color: red; text-align: center">Nėra pateiktų pranešimų!</h2>';
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