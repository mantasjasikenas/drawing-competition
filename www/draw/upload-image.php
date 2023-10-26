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
                    $topic = $database->getCurrentCompetition()['topic'];
                    echo '<h2 style="margin-bottom: unset">Konkurso tema</h2>';
                    echo '<h3 style="margin-top: unset; color: rgba(50,194,137,0.62)">' . $topic . '</h3>';
                    ?>

                </div>
                <br>

                <div style="padding: 10px; text-align: center">
                    <form enctype="multipart/form-data" method="post" action="actions/upload.php">
                        <label for="files">Pasirinkite piešinių nuotraukas</label><br>
                        <input name="images[]" multiple type="file" id="file[]" accept="image/*" required><br><br>
                        <textarea required name="comment" placeholder="Komentaras"
                                  cols="40" rows="5"></textarea
                        >
                        <br><br>
                        <input type="submit" value="Pateikti">
                    </form>
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