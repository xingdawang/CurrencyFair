<?php

    /**
     * @author: Xingda Wang
     * @date: 13th / May / 2015
     * @version 1.0
     */

    $url = "http://212.111.43.104/old/message_consumption.php";
    $json = file_get_contents($url);
    $data = json_decode($json);
    $user_id = $data['user_id'];
    $currency_from  = $data['currency_from'];
    $currency_to = $data['currency_to'];
    $currency_from = $data['amount_sell'];
    $rate = $data['rate'];
    $time_placed = $data['time_placed'];
    $originating_country = $data['originating_country'];

    // create image
    $image = imagecreatetruecolor(100, 100);
    
    // allocate some colors
    $white    = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
    $gray     = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
    $darkgray = imagecolorallocate($image, 0x90, 0x90, 0x90);
    $navy     = imagecolorallocate($image, 0x00, 0x00, 0x80);
    $darknavy = imagecolorallocate($image, 0x00, 0x00, 0x50);
    $red      = imagecolorallocate($image, 0xFF, 0x00, 0x00);
    $darkred  = imagecolorallocate($image, 0x90, 0x00, 0x00);
    
    // Test data
    #$currency_from = 1000;
    #$currency_to = 747;
    
    $ratio = $currency_from / ($currency_from + $currency_to) * 360;
    //echo $ratio;
    imagefilledarc($image, 50, 50, 100, 50, 0, $ratio , $gray, IMG_ARC_PIE); # "Grey part is the currency from";
    imagefilledarc($image, 50, 50, 100, 50, $ratio, 360 , $red, IMG_ARC_PIE); # "Red part is the currency to";
    
    // flush image. render a graph of processed data from the messages consumed.
    header('Content-type: image/png');
    imagepng($image);
    imagedestroy($image);
?>