<?php
global $session, $database;
include("include/session.php");
if ($session->logged_in && $session->isEvaluator()) {
    ?>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Įvertinti paveikslėlį</title>
        <link href="include/styles.css" rel="stylesheet" type="text/css"/>
        <style>
            a.left-arrow {
                color: black;
                background-color: darkseagreen;
                font-size: 40px;
                border-radius: 50% 0 0 50%;
            }

            a.right-arrow {
                color: black;
                background-color: darkseagreen;
                font-size: 40px;
                border-radius: 0 50% 50% 0;
            }

            a.arrow {
                text-decoration: none;
                display: inline-block;
                padding: 4px 8px 8px;
            }
        </style>
        <script>
            function toggleFilter() {
                const img = document.getElementById('img');
                if (img.style.filter === 'grayscale(100%)') {
                    img.style.filter = 'none';
                } else {
                    img.style.filter = 'grayscale(100%)';
                }
            }
        </script>
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
                    if (isset($_SESSION['message'])) {
                        echo "<h4 style='color: rgb(130,255,47)'>" . $_SESSION['message'] . "</h4>";
                        unset($_SESSION['message']);
                    }
                    ?>
                </div>
                <br>

                <div style="padding: 10px">
                    <?php

                    $unrated_images = $database->getUnratedPaintingsId($session->id);


                    if (!($unrated_images && count($unrated_images) > 0)) {
                        echo '<h2 style="margin-top: unset; color: red; text-align: center">Nėra neįvertintų paveikslėlių</h2>';
                    } else {
                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center">';

                        $id = $_GET['image'] ?? $unrated_images[0];

                        if (!in_array($id, $unrated_images)) {
                            echo '<h2 style="margin-top: unset; color: red; text-align: center">Nurodytos nuotraukos nepavyko rasti!</h2>';
                            echo '<br><br>';
                            echo '<a href="rate-image.php">Grįžti atgal</a>';

                            exit();
                        }

                        $image = $database->getPaintingById($id);
                        $imageData = $image['image'];
                        $index = array_search($id, $unrated_images);

                        echo '<div style="display: flex; align-items: center">';

                        if (count($unrated_images) > 1 && $index > 0) {
                            $prev_id = $unrated_images[$index - 1];

                            echo "<a class='arrow left-arrow' href='rate-image.php?image=$prev_id'>&#8249;</a>";
                        }

                        echo '<img id="img" src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="Uploaded Image" style="height: 200px;">';


                        if (count($unrated_images) > 1 && $index < count($unrated_images) - 1) {
                            $next_id = $unrated_images[$index + 1];
                            echo "<a class='arrow right-arrow'
                                    href='rate-image.php?image=$next_id'>&#8250;</a>";
                        }

                        echo '</div>';
                        echo '</div>';

                        echo '<div style="display: flex; flex-wrap: wrap; justify-content: center">';
                        echo '<button style="margin: 20px; height: 30px; 
                                width: 100px; background-color: darkseagreen; border-radius: 5px; border: none; 
                                color: white; font-size: 16px; font-weight: bold; cursor: pointer;" 
                                onclick="toggleFilter()"
                                >B/W filtras</button>';


                        echo '
                        <div>
                            <form method="POST" action="actions/handle-report.php" style="justify-content: center">
                                <input type="hidden" name="painting_id" value=' . $id . '>
                                    
                                <input type="text" name="cause" placeholder="Priežastis" style="margin-top: 20px; height: 30px;
                                    width: 200px; border-radius: 5px; 
                                    color: black; font-size: 16px; font-weight: bold;">
                                    
                                    <br>
                                    
                                    <input 
                                    type="submit"
                                    maxlength="254"
                                    name="report"
                                    value="Pranešti"
                                    style="margin-top: 5px; height: 30px; 
                                    width: 100px; background-color: red; border-radius: 5px; border: none; 
                                    color: white; font-size: 16px; font-weight: bold; cursor: pointer;">
                            </form>
                        </div>';

                        echo '</div>';


                        echo "<div style=' padding-top: 24px; display: flex; flex-wrap: wrap; justify-content: center'>
                                <form method='POST' action='actions/handle-review.php' style='justify-content: center'>
                                    <input type='hidden' name='painting_id' value='$id'>  
                                    
                                    <label for='composition'>Kompozicija:</label><br>
                                    <input type='range' id='composition' name='composition' min='0' max='10'><br><br>
                                    
                                    <label for='colorfulness'>Spalvingumas:</label><br>
                                    <input type='range' id='colorfulness' name='colorfulness' min='0' max='10'><br><br>
                                    
                                    <label for='compliance'>Atitikimas tematikai:</label><br>
                                    <input type='range' id='compliance' name='compliance' min='0' max='10'><br><br>
                                    
                                    <label for='originality'>Originalumas:</label><br>
                                    <input type='range' id='originality' name='originality' min='0' max='10'><br><br>
                                    
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

