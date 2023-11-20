<?php
//Formuojamas meniu.
if (isset($session) && $session->logged_in) {
    $path = "";
    if (isset($_SESSION['path'])) {
        $path = $_SESSION['path'];
        unset($_SESSION['path']);
    }
    ?>
    <table width=100% border="0" cellspacing="1" cellpadding="3" class="meniu">
        <?php
        echo "<tr><td>";
        echo "Prisijungęs vartotojas: <b>$session->username</b> <br>";
        echo "Vartotojo tipas: <b>";
        if ($session->isParticipant()) {
            echo "dalyvis";
        } else if ($session->isEvaluator()) {
            echo "vertintojas";
        } else if ($session->isAdmin()) {
            echo "administratorius";
        }
        echo "</td></tr><tr><td>";
        echo "[<a href=\"" . $path . "index.php\">Pagrindinis puslapis</a>] &nbsp;&nbsp;"
            . "[<a href=\"" . $path . "userinfo.php?user=$session->username\">Mano paskyra</a>] &nbsp;&nbsp;"
            . "[<a href=\"" . $path . "useredit.php\">Redaguoti paskyrą</a>] &nbsp;&nbsp;";

        if ($session->isParticipant()) {
            echo "[<a href=\"" . $path . "upload-image.php\">Įkelti paveikslėlį</a>] &nbsp;&nbsp;"
                . "[<a href=\"" . $path . "portfolio.php\">Portfolio</a>] &nbsp;&nbsp;";
        }

        if ($session->isEvaluator()) {
            echo "[<a href=\"" . $path . "rate-image.php\">Įvertinti paveikslėlį</a>] &nbsp;&nbsp;";
        }

        if ($session->isParticipant() || $session->isEvaluator()) {
            echo "[<a href=\"" . $path . "gallery.php\">Galerija</a>] &nbsp;&nbsp;";
        }

        if ($session->isAdmin()) {
            echo "[<a href=\"" . $path . "reports.php\">Pranešimai</a>] &nbsp;&nbsp;";
            echo "[<a href=\"" . $path . "admin/admin.php\">Administratoriaus sąsaja</a>] &nbsp;&nbsp;";
        }

        echo "[<a href=\"" . $path . "process.php\">Atsijungti</a>]";
        echo "</td></tr>";
        ?>
    </table>
    <?php
}
?>