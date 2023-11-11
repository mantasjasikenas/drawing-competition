<?php
global $session;
include("../include/session.php");

global $database;

$painting_id = $_POST['painting_id'];
$cause = $_POST['cause'];

$res = $database->reportPainting($painting_id, $session->id, $cause);

if ($res) {
    $_SESSION['message'] = "Administratorius informuotas apie netinkamą paveikslėlį!";
} else {
    $_SESSION['message'] = "Nepavyko pranešti!";
}

header("Location: ../rate-image.php");
exit();

