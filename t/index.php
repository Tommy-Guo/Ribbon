<?php
    require "../mySQL_details.php";
    $database = new mysqli($sql_host, $sql_user, $sql_password, "ribbon");

    $current_url = htmlspecialchars( "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", ENT_QUOTES, 'UTF-8' );

    if ($_GET['alias'] != "index.php") {
        $databaseQuery = $database->query("SELECT content, type FROM ribbons WHERE alias = '".$_GET['alias']."'");
        while ($data = $databaseQuery->fetch_row()) {
            if ($data[1] == "link") {
                header("Location: $data[0]");
            } elseif (($data[1] == "text") || ($data[1] == "code")) {
                echo "<xmp>" . $data[0] . "</xmp>";
            } elseif ($data[1] == "file") {
                header("Location: " . "../uploads/" . $data[0]);
            }
        }
    }
?>