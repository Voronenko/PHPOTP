<?php
/**
 * Created by PhpStorm.
 * Date: 3/26/2017
 * Time: 4:15 PM
 */
include("../code/Verificator.php");
include("../code/BarCode.php");
include("config.php");

use Voronenko\PHPOTP\Logger;
use Voronenko\PHPOTP\Verificator;
use Voronenko\PHPOTP\BarCode;

$verificator = new Verificator(APP_SECRET, FUZZINESS);
echo "<h1>Sign up form:</h1>";
echo BarCode::generate( APP_SECRET, "User_alias", "My_company", "My_application");
?>

<br/>
<h1>Sign in form:</h1>
<form action="/example/index.php" method="get">
    <label>Enter code, that mobile app generated:</label>
    <br/>
    <input id="code" type="text" placeholder="Code from auth. app" name="code"/>
    <input type="submit" value="submit">
</form>

<?php

if (isset($_GET["code"])) {
    if ($verificator->verify($_GET["code"])) {
        echo "<div style='color:green;'>Code is valid</div>";
    } else {
        echo "<div style='color:red;'>Code is not valid</div>";
    }
}
if (DEBUG) {
    echo "Debug trace: ";
    echo "<br/>";
    foreach (Logger::getTrace() as $message) {
        echo "<div>$message</div>";
    }
}
?>
