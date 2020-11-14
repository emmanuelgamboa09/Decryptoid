<?php
require_once 'functions.php';
echo <<<_END

    <form action='signin.php' method='post' enctype='multipart/form-data'><pre>
        <input type='text' name='username' placeholder='Username'>
        <input type='text' name='password' placeholder='Password'>
        
        <button type='submit' name='login'>Login</button>
        <p>or</p>
        <button><a href = signup.php>Sign up </a></button>
        
    </pre></form>
    
    
_END;
echo <<<_SEARCH

     <form action='final.php' method='post' enctype='multipart/form-data'><pre>
        <p>Translate:</p>
        <input type='text' name='translate' placeholder='Translate'>
        
        <button type='submit' name='word_translation'>Translate</button>
    </pre></form>

_SEARCH;
//default search
if (isset($_POST['word_translation'])) {
    $search = mysql_entities_fix_string($conn, $_POST['translate']);
    $query  = "SELECT * FROM `translation` WHERE `user` IS NULL AND `english` = '$search'";
    $result = $conn->query($query);
    if (!$result)
        die("*insert puppies here*");
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if ($row['english'] == $search) {
        echo "<br>" . $search . " translates to: " . $row['other_lang'];
    } else {
        echo "<br>Translation not found";
    }
    $result->close();
}
$conn->close();
?>