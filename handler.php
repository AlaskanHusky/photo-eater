<?php

#https://vk.com/album12613550_169632134

function getJSONData($filePath)
{
    $string = file_get_contents($filePath); // get templates of resources from file
    $json_a = json_decode($string, true); // if true, returned objects will be converted into associative arrays
    return $json_a;
}

function isValidLink($link)
{
    $json_array = getJSONData("configs/links.json");
    $isValid = false;
    $key = null;
    foreach ($json_array as $resource => $template) {
        $entry = stripos($link, $template);
        if (!($entry === false)) {
            $isValid = true;
            $key = array_search($template, $json_array); // searches given value in an array, return key or false
        }
    }
    return array($isValid, $key);
}

function referToHandler($name)
{
    switch ($name) {
        case "Vkontakte":
            header("Location: vkontakte.php");
            exit;
            break;
        case "Instagram":
            echo "3";
            break;
        case "DeviantArt":
            echo "4";
            break;
        case "Pinterest":
            echo "5";
            break;
    }
}

if (isset($_POST['link'])) {

    $link = addslashes($_POST['link']);
    setcookie("link", $link, time() + 3600 * 24);
    $isValid = isValidLink($link);
    if ($isValid[0]) {
        setcookie("name", $isValid[1], time() + 3600 * 24);
        referToHandler($isValid[1]);
    } else {
        echo "Invalid!";
    }

}
?>


