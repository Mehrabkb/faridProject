<?php


require 'inc/DB.php';

try {

    $db = new DB();

    $result = $db->query("SELECT 1");

    echo "DB Connected Successfully ✅";

} catch (Exception $e) {

    echo "Connection Failed ❌ <br>";
    echo $e->getMessage();

}
