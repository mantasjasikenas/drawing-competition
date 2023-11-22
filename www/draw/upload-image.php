<?php
global $session, $database, $form;
include("include/session.php");


if ($session->logged_in && $session->isParticipant()) {
    ?>
    <html lang="lt">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8"/>
        <title>Įkelti paveikslėlį</title>

        <link href="include/styles.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="include/style.php" media="screen">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
                <div style="display: flex; flex-wrap: wrap; flex-direction: column; align-items: center;">
                    <h1 style="margin-bottom: unset;">Įkelti paveikslėlį</h1>
                    <?php

                    if (isset($_SESSION['message'])) {
                        echo '<div class="success-msg">
                                <i class="fa fa-check"></i>' . $_SESSION['message'] . '
                             </div>';

                        unset($_SESSION['message']);
                    }

                    if ($form->num_errors > 0) {
                        echo '<div class="error-msg">
                            <i class="fa fa-check"></i>' . 'Įvyko klaida!' . '
                         </div>';
                    }


                    ?>
                </div>
                <br>

                <div style="padding: 10px; text-align: center">

                    <?php
                    $competitions = $database->getAvailableCompetitions();

                    if ($competitions && count($competitions) > 0) {
                        echo '<form enctype="multipart/form-data" method="post" action="actions/handle-upload.php">';

                        echo '<label for="competition">Pasirinkite konkursą</label><br>';
                        echo '<select name="competition" id="competition">';
                        foreach ($competitions as $competition) {
                            echo '<option value="' . $competition['id'] . '" data-image="' . base64_encode($competition['image']) . '">' . $competition['topic'] . ' ' . $competition['start_date'] . ' - ' . $competition['end_date'] . '</option>';

                        }
                        echo '</select><br><br>';

//                            show competion image
                        echo '<img id="competition_image" style="height: 160px; object-fit: cover" src="data:image/jpeg;base64,' . base64_encode($competitions[0]['image']) . '"/><br><br>';

                        echo '<label for="style">Pasirinkite paveikslėlių stilių</label><br>';
                        echo '<select name="style[]" id="style" multiple>';
                        echo '<option value="border">Rėmelis</option>';
                        echo '<option value="rounded">Apvalūs kampai</option>';
                        echo '<option value="oval">Ovalas</option>';
                        echo '</select><br><br>';

                        echo '<label for="files">Pasirinkite piešinių nuotraukas</label><br>
                            <input name="images[]" multiple type="file" id="file[]" accept="image/*">
                            <br> ' . $form->error("images") . '
                            <!--oninvalid="this.setCustomValidity("Privaloti pateikti bent vieną piešinį.")"
                            onchange="this.setCustomValidity("")-->
                            <br><br>

                            <textarea name="comment" placeholder="Komentaras"
                                      cols="40" rows="5">' . $form->value("comment") . '</textarea>
                            <br> ' . $form->error("comment") . '
                            <br><br>
                            <input type="submit" value="Pateikti">
                    </form>';

                    } else {
                        echo '<h2 style="margin-top: unset; color: red; text-align: center">Šiuo metu nevyksta nei vienas konkursas!</h2>';
                    }
                    ?>

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

    <script>
        $('#competition').change(function () {
            const selected_option = $('#competition').find(":selected");
            const image_data = selected_option.data('image');
            console.log("Image data: ", image_data);
            console.log("Selected option: ", selected_option);

            $('#competition_image').attr('src', 'data:image/jpeg;base64,' + image_data);
        });
    </script>
    </body>
    </html>
    <?php
    //Jei vartotojas neprisijungęs, užkraunamas pradinis puslapis  
} else {
    header("Location: index.php");
}
?>