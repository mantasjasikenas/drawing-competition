<?php
global $session, $database;
include("include/session.php");
//Jei prisijunges Administratorius ar Valdytojas vykdomas operacija3 kodas
if ($session->logged_in && ($session->isAdmin() || $session->isEvaluator())) {
    ?>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Įvertinti paveikslėlį</title>
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
                    <h1>Įvertinti paveikslėlį</h1>
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
                    $unrated_images = glob($dir . "*.{jpg,png,gif,svg}", GLOB_BRACE);
                    $unrated_images = array_slice($unrated_images, 0, 5);

                    if (count($unrated_images) == 0) {
                        echo '<h2 style="margin-top: unset; color: red; text-align: center">Nėra neįvertintų paveikslėlių</h2>';
                    } else {
//                        echo '<h2 style="margin-top: unset; text-align: center">Geriausi 10 darbų</h2>';

                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center">';

//                        add images to select radio buttons


                        for ($i = 0; $i < count($unrated_images); $i++) {
                            echo '<div style="padding: 6px;">';

                            echo '<input type="radio" id="image' . $i . '" name="image" value="' . $unrated_images[$i] . '">';

                            echo '<label for="image' . $i . '">
                                    <img height="200" src="' . $unrated_images[$i] . '" />
                                  </label>';


                            $image_name = basename($unrated_images[$i]);


                            echo '</div>';
                        }


                        echo '</div>';

                        echo "<div style=' padding-top: 24px; display: flex; flex-wrap: wrap; justify-content: center'>
                                <form method='POST' action='handle_review.php' style='justify-content: center'>
                                    <input type='hidden' name='image_name' value='" . $image_name . "'>
                                    
                                    <label for='vol'>Kompozicija:</label><br>
                                    <input type='range' id='vol' name='vol' min='0' max='10'><br><br>
                                    
                                    <label for='vol'>Spalvingumas:</label><br>
                                    <input type='range' id='vol' name='vol' min='0' max='10'><br><br>
                                    
                                    <label for='vol'>Atitikimas tematikai:</label><br>
                                    <input type='range' id='vol' name='vol' min='0' max='10'><br><br>
                                    
                                    <label for='vol'>Originalumas:</label><br>
                                    <input type='range' id='vol' name='vol' min='0' max='10'><br><br>
                                    
                                    <input type='submit' value='Palikti įvertinimą'>
                                </form>
                              </div>
                              ";
                        echo '</div>';


                    }

                    ?>
                    <br>
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
    //Jei vartotojas neprisijungęs arba prisijunges, bet ne Administratorius 
    //ar ne Valdytojas - užkraunamas pradinis puslapis   
} else {
    header("Location: index.php");
}
?>

