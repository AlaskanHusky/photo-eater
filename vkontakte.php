<?php

define('TOKEN', '7821c4867821c4867821c486df787a2b26778217821c4862132c7c7ab0e8f3569664690');

class Vk
{
    private $token;
    private $version = '5.64';

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function photosGet($data)
    {
        return $this->request('photos.get', $data);
    }

    private function request($method, array $params)
    {
        $params['v'] = $this->version;

        $ch = curl_init('https://api.vk.com/method/' . $method . '?access_token=' . $this->token);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $data = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($data, true);

        if (!isset($json['response'])) {
            throw new \Exception($data);
        }
        usleep(mt_rand(1000000, 2000000));
        return $json['response'];
    }
}

function getId($link)
{
    $re = "/https:\/\/vk.com\/album[-]?([0-9]+)_([0-9]+)/i";
    preg_match_all($re, $link, $matches);
    $object_id = $matches[1][0];
    $album_id = $matches[2][0];
    if (strpos($link, '-')) {
        $object_id = '-' . $matches[1][0];
    }
    if ($matches[2][0] == 0) {
        $album_id = -7;
    }
    if ($matches[2][0] == 00) {
        $album_id = -6;
    }
    return array($object_id, $album_id);
}

function getData($ids)
{

    $token = TOKEN;

    $object_id = $ids[0];
    $album_id = $ids[1];

    $vk = new Vk($token);

    $result = $vk->photosGet([
        'owner_id' => $object_id,
        'album_id' => $album_id,
    ]);
    return $result;
}

function getHighRes($photo_info)
{
    $res = array('2560', '1280', '807', '604', '130', '75');
    foreach ($res as $size) {
        if (array_key_exists('photo_' . $size, $photo_info)) {
            return 'photo_' . $size;
        }
    }

}

function createReferencesList($data)
{
    $count = $data["count"];
    $photos = $data["items"];
    $imgs = array();
    foreach ($photos as $photo_info) {
        $res = getHighRes($photo_info);
        $imgs[] = $photo_info[$res];
        #date('i:H d-m-Y', $photo_info['date']);
    }
    return $imgs;
}

function downloadImages($ref_list)
{
    $re = "/https:\/\/pp.userapi.com\/.+\/.+\/.+\/(.+.jpg)/i";
    foreach ($ref_list as $fullname) {
        preg_match_all($re, $fullname, $matches);
        $name = $matches[1][0];
        $file = file_get_contents($fullname);
        file_put_contents('data/photos/' . $name, $file);
    }
}

$ids = getId($_COOKIE['link']);
$data = getData($ids);
$ref_list = createReferencesList($data);
downloadImages($ref_list);

header("Location: photos.php");
exit;

?>