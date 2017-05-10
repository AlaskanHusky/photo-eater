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

function createElements($files_list)
{
    for ($i = 0; $i < 14; $i++) {
        echo "<div class='row' id='" . "_" . $files_list[$i] . "'>
                <img src='/data/photos/" . $files_list[$i] . "' class='image'></div>";
    }
}

function createArchive($files_list)
{
    $path = "data/photos/";
    $archive_name = "test";
    $zip_name = $archive_name . ".zip"; // filename
    $error = "";
    $zip = new ZipArchive();

    if ($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE) {
        $error .= "Zip creation failed";
    }

    for ($i = 0; $i < count($files_list); $i++) {
        $zip->addFile($path . $files_list[$i]); // add file to zip archive
    }

    if (file_exists($zip_name)) {
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=$zip_name");
        header("Content-Length: " . filesize($zip_name));
        readfile($zip_name); // send archive to user
        unlink($zip_name); // delete file
    }
    $zip->close();
}

$filenames = getFilenames();
session_start();
$_SESSION['filenames'] = $filenames;

if (isset($_POST['download'])) {
    createArchive($_SESSION['filenames']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Photos</title>
    <link rel="stylesheet" type="text/css" href="/css/logo.css">
    <link rel="stylesheet" type="text/css" href="/css/photos.css">
    <script type="text/javascript" src="libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<a href="index.php">
    <div class="logo">Photo Eater</div>
</a>
<div class="photos">
    <div class="row">
        <form action="photos.php" method="post">
            <input type="submit" class="btn-download" name="download" value="Download all">
        </form>
    </div>
    <?php
    createElements($_SESSION['filenames']);
    ?>
    <div class="load">
        <input type="submit" class="btn-download" value="Load more">
        <img src="loading.gif" id="imgLoad">
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#imgLoad").hide();  //Скрываем прелоадер
        });
        var num = 14; //чтобы знать с какой записи вытаскивать данные
        $(function () {
            $(".load div").click(function () { //Выполняем если по кнопке кликнули
                $("#imgLoad").show(); //Показываем прелоадер

                $.ajax({
                    url: "ajax.php",
                    type: "GET",
                    data: {"num": num},
                    cache: false,
                    success: function (response) {
                        if (response == 0) {  // смотрим ответ от сервера и выполняем соответствующее действие
                            alert("Больше нет записей");
                            $("#imgLoad").hide();
                        } else {
                            $("#content").append(response);
                            num = num + 14;
                            $("#imgLoad").hide();
                        }
                    }
                });
            });
        });
    </script>

</div>
</body>
</html>