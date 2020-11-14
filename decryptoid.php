<?php
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error)
    die("OOPS");

echo <<<_END
<html><head><title>DECTRYPTOID</title></head><body>
<form method='post' action='decryptoid.php' enctype='multipart/form-data'><pre>
Select File or input text into the box:
<input type='file' name='filename'>
<input type='text' name='txt' placeholder='txt'>
<select name="option">
  <option value="1">Decrypt</option>
  <option value="2">Encrypt</option>
</select>
<select name="option2">
  <option value="1">Simple Substitution</option>
  <option value="2">Double Transposition</option>
  <option value="3">RC4</option>
  <option value="4">DES</option>
</select>
<input type='text' name='key' placeholder='key'>
<input type='submit' value='Upload'>
</pre>
</form>
_END;

if ($_FILES || isset($_POST['txt'])) 
{
    if (file_exists($_FILES['filename']['tmp_name']))
        $content = htmlentities(file_get_contents($_FILES['filename']['tmp_name']));
    else
        $content = mysql_entities_fix_string($conn, $_POST['txt']);
        $key = mysql_entities_fix_string($conn, $_POST['key']);

    if ($_POST["option2"] == 1)
    {
        echo simple_substitution($content);
    }
    else if ($_POST["option2"] === 2) 
    {
        
    }
    else if ($_POST["option2"] == 3) 
    {
        echo rc4($content, $key);
        
    } 
    else if ($_POST["option2"] == 4) 
    {
        
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

function simple_substitution($text)
{
    $output = "";
    $alphabet = "abcdefghijklmnopqrstuvwxyz ";
    $otheralphabet = "mcwsejlyokrfuibnatphvxqdzg ";

    if ($_POST["option"] == 1) {
        $first = $alphabet;
        $second = $otheralphabet;
    } else {
        $first = $otheralphabet;
        $second = $alphabet;
    }

    for ($i = 0; $i < strlen($text); ++ $i) {
        $oldCharIndex = strpos($second, strtolower($text[$i]));
        if ($oldCharIndex !== false) {
            $oldCharIndex = strpos($first, strtolower($text[$i]));
            $output .= ctype_upper($text[$i]) ? strtoupper($second[$oldCharIndex]) : $second[$oldCharIndex];
        } else {
            $output .= $text[$i];
        }
    }
    return $output;
}

function rc4($text, $key)
{
    $array = array();
    //$t = array();
    //$text = unpack('H*', $text);
    //$text = strval($text);
    //$text = implode("", $text);
    //$text = decbin(ord($text));
    
    for($i = 0; $i < 256; $i++)
    {
        $array[$i] = $i;
        //$t[$i] = $key[$array[$i] % strlen($key)];
    }

    $j = 0;

    for($i = 0; $i < 256; $i++)
    {
        $j = ($j + $array[$i] + ord($key[$i % strlen($key)])) % 256;

        $temp = $array[$i];
        $array[$i] = $array[$j];
        $array[$j] = $temp;
    }

    $i = 0;
    $j = 0;
    $out = '';

    for($k = 0; $k < strlen($text); $k++)
    {
        $i = ($i + 1) % 256;
        $j = ($j + $array[$i]) % 256;

        $temp = $array[$i];
        $array[$i] = $array[$j];
        $array[$j] = $temp;

        $out .= $text[$k] ^ chr($array[($array[$i] + $array[$j]) % 256]);
        //echo "OUTPUT: ";
        //echo $out;
        //echo "<br>";
    }

    //$out = pack('H*', base_convert($out, 2, 16));
    //$out = chr(bindec($out));

    return $out;
}

?>
