<?php
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error)
    die("*Insert puppies here*");
function validateUsername($field)
{
    if ($field == "")
        return "No Username was entered.\n";
    else if (strlen($field) < 5)
        return "Username must be at least 6 character. \n";
    else if (preg_match("/[^a-zA-Z0-9_-]/", $field))
        return "Only a-z, A-Z, 0-9, - and _ are allowed in usernames .\n";
    return "";
}
function validatePassword($field)
{
    if ($field == "")
        return "No Password was entered.\n";
    else if (strlen($field) < 6)
        return "Password must be at least 6 character. \n";
    else if (!preg_match("/[a-z]/", $field) || !preg_match("/[A-Z]/", $field) || !preg_match("/[0-9]/", $field))
        return "1 of each a-z, A-Z, 0-9 in the password  .\n";
    return "";
}
function mysql_entities_fix_string($conn, $string)
{
    return htmlentities(mysql_fix_string($conn, $string));
}
function mysql_fix_string($conn, $string)
{
    if (get_magic_quotes_gpc())
        $string = stripslashes($string);
    return $conn->real_escape_string($string);
}
?>