<?php

require "vendor/autoload.php";

use TwitterNoAuth\Twitter;

$mert = new Twitter();
//print_r($mert->getTweets("#corona"));
//print_r($mert->getTrends());
//print_r($mert->getProfile());

foreach ($mert->getTweets("#corona") as $item){
    print_r($mert->getProfile($item["username"]));
}
