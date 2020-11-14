<?php
session_start();
require_once 'functions.php';
if (isset($_POST['login'])) {
    if (isset($_POST['username']))
        $_SERVER['PHP_AUTH_USER'] = mysql_entities_fix_string($conn, $_POST['username']);
    if (isset($_POST['password']))
        $_SERVER['PHP_AUTH_PW'] = mysql_entities_fix_string($conn, $_POST['password']);
}
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    $un_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
    $pw_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);
    $fail    = validateUsername($un_temp);
    $fail .= validatePassword($pw_temp);
    if ($fail != "") {
        echo $fail;
        die("<p><a href = final.php>Click here return </a></p>");
    } else {
        $query  = "SELECT * FROM users WHERE name = '$un_temp'";
        $result = $conn->query($query);
        if (!$result)
            die("error");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($un_temp == $row['name']) {
            $token = hash('ripemd128', $row['salt'] . $pw_temp);
            if ($row['hash'] == $token) {
                session_start();
                $_SESSION['username'] = $un_temp;
                die("<p><a href = continue.php>Click Here to Continue </a></p>");
            } else {
                echo "Invalid username/password";
                die("<p><a href = final.php>Click here to sign in </a></p>");
            }
        } else {
            echo "Invalid username/password";
            die("<p><a href = final.php>Click here to sign in </a></p>");
        }
    }
} else {
    die("<p><a href = final.php>Enter Your Credentials. Click here return </a></p>");
}
$conn->close();
?>