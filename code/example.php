<?php
/**
 * Created by PhpStorm.
 * User: oty
 * Date: 3/26/2017
 * Time: 4:15 PM
 */
include("Verificator.php");
include("BarCode.php");

const APP_SECRET = "GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ";

$verificator = new \AshaTob\GoogleAuth\Verificator(APP_SECRET);
echo \AshaTob\GoogleAuth\BarCode::generate("AshaTob", "My_company", APP_SECRET, "My_application");

if (isset($_GET["code"])) {
    if ($verificator->verify($_GET["code"])) {
        echo "<div style='color:green;'>Code is valid</div>";
    } else {
        echo "<div style='color:red;'>Code is not valid</div>";
    }
}
?>
<br/>
<form action="example.php" method="get">
    <label for="code">Code:</label>
    <input id="code" type="text" placeholder="Code from auth. app" name="code"/>
    <input type="submit" value="submit">
</form>
