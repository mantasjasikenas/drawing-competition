<?php
global $session;
include("../include/session.php");

global $database;

// Make connection
$conn = $database->connection;

// Check connection and throw error if not available
if ($conn->connect_error) {
    die("Connection failed in upload.php: " . $conn->connect_error);
}

// Check if an image file was uploaded
if (isset($_FILES["images"])) {
    $competition_id = $_POST['competition'];
    $styles = $_POST['style'];

    $style = "";

    foreach ($styles as $style_item) {
        $style .= $style_item . " ";
    }

    if ($style == "default") {
        $style = "";
    }

    $_SESSION['style'] = $style;

//    TODO add style to database
// convert style to css class


    $sql = "INSERT INTO uploads (comment, style, creation_date, fk_user, fk_competition) 
            VALUES ('" . $_POST['comment'] . "', '" . $style . "', NOW(), '" . $session->id . "', '" . $competition_id . "')";
    $statement = $conn->prepare($sql);
    $statement->execute();

//    echo error
    echo mysqli_error($conn);

    $upload_id = mysqli_insert_id($conn);

    foreach ($_FILES['images']['name'] as $key => $value) {
        $imgContent = file_get_contents($_FILES['images']['tmp_name'][$key]);

        $sql = "INSERT INTO paintings(id, image, fk_upload) VALUES(?, ?, ?)";

        $id = null;
        $statement = $conn->prepare($sql);
        $statement->bind_param('sss', $id, $imgContent, $upload_id);


        $current_id = $statement->execute(); //or die(" < b>Error:</b > Problem on Image Insert < br />" . mysqli_connect_error());


        if (!empty($current_id)) {
            $message = "Piešiniai įkelti sėkmingai.";

        } else {
            $message = "Nepavyko įkelti failo. Pabandykite dar kartą." . mysqli_error($conn);
        }

        $_SESSION['message'] = $message;
        $_SESSION['upload_id'] = $upload_id;
    }


} else {
    echo "Please select an image file to upload . ";
}

// Close the database connection
$conn->close();

header("Location: ../upload-image.php");
exit();