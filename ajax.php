<?php
function getFilenames()
{
    $path = "data/photos";
    $photos = scandir($path);
    unset($photos[array_search('.', $photos)]);
    unset($photos[array_search('..', $photos)]);
    sort($photos);
    return $photos;
}

if (isset($_GET['num'])) {
    $num = $_GET['num'];
    $files_list = getFilenames();
    if ($num == (count($files_list))) {
        echo 0;
    } else {
        echo "<div class='row' id='" . "_" . $files_list[$num] . "'>
                <img src='/data/photos/" . $files_list[$num] . "' class='image'></div>";
        sleep(2);
    }
}
?>