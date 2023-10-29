<?php
global $session;
include("../include/session.php");

global $database;


$composition = $_POST['composition'];
$colorfulness = $_POST['colorfulness'];
$compliance = $_POST['compliance'];
$originality = $_POST['originality'];

$painting_id = $_POST['painting_id'];

$res = $database->createReview($session->id, $painting_id, $composition, $colorfulness, $compliance, $originality);

if ($res) {
   $_SESSION['message'] = "Įvertinimas įrašytas sėkmingai!";
} else {
    $_SESSION['message'] = "Nepavyko įrašyti įvertinimo!";
}

header("Location: ../rate-image.php");
exit();

