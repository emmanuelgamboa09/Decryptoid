<?php
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error)
    die("OOPS");

echo <<<_END
   <html><head><title></title></head><body>
   <p>Sign up an account<p>
   <form method='post' action='signup.php' enctype='multipart/form-data'><pre>
   Email: <input type='text' name='email'>
   Username: <input type='text' name='username'>
   Password: <input type = 'text' name = 'password'>
   Re-enter your password: <input type = 'text' name ='re_password'><br><br>
   <button type='submit' name='signup'>Sign up</button>
   </pre>
   </form>
_END;

if (isset($_POST['signup'])) {
    if (isset($_POST['email'])) // email is not empty
        $email_temp = mysql_entities_fix_string($conn, $_POST['email']);

    if (isset($_POST['username'])) // username is not empty
        $username_temp = mysql_entities_fix_string($conn, $_POST['username']);

    if (isset($_POST['password'])) // password is not empty
        $password_temp = mysql_entities_fix_string($conn, $_POST['password']);

    if (isset($_POST['re_password'])) // re_password is not empty
        $re_password_temp = mysql_entities_fix_string($conn, $_POST['re_password']);

    $username_error = usernameCheck($username_temp);
    $password_error = passwordCheck($password_temp);
    $email_error = emailCheck($email_temp);

    if ($username_error != "")
        echo $username_error;
    else if ($password_error != "")
        echo $password_error;
    else if ($email_error != "")
        echo $email_error;
    else if (strcmp($password_temp, $re_password_temp) != 0)
        echo "Passwords are not the same";
    else {
        $query = "SELECT * FROM users WHERE username = '$username_temp'";
        $result = $conn->query($query);
        if (! $result)
            die("error");

        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($username_temp == $row['username']) {
            echo "Username taken!";
        } else {
            $numberOfBytes = 8;
            $salt = random_bytes($numberOfBytes);
            $token = hash('ripemd128', "$salt$password_temp");
            $key = generate_key();
            $query = "INSERT INTO users(email, username, password, salt, key) VALUES ('$email_temp', '$username_temp', '$token', '$salt', '$key')";
            $result = $conn->query($query);
            if (! $result)
                die("OOPS");
            
            
            echo "Your key is $key. Note it down!";
            echo "You are now Signed Up <p><a href = login.php>. Click this link to continue </a></p>");
        }
    }
}

$conn->close();

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

function usernameCheck($username)
{
    if ($username == "")
        return "Username is required" . "<br>";
    else if (strlen($username) < 5)
        return "Username must be at least 5 character" . "<br>";
    else if (! preg_match('/[^a-z_\-0-9]/i', $username))
        return "Only a-z, A-Z, 0-9, - and _ are allowed in usernames" . "<br>";

    return "";
}

function passwordCheck($password)
{
    if ($password == "")
        return "Password is required" . "<br>";
    else if (strlen($password) < 6)
        return "Password must be at least 6 character" . "<br>";
    else if (! preg_match('/[^a-z_\-0-9]/i', $password))
        return "Only a-z, A-Z, 0-9, - and _ are allowed in passwords" . "<br>";

    return "";
}

function emailCheck($email)
{
    if ($email == "")
        return "Email is required" . "<br>";
    else if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }

    return "";
}

function generate_key()
{
    $length = 4;
    $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for(int $i = 0;
    $i < $length; $i++)
    {
        $charRand = rand(0, 26);
        $key .= $charset[$charRand];
    }

    return $key;
}