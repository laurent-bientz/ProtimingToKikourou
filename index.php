<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 09/10/2019
 * Time: 11:11
 */

require "vendor/autoload.php";

use App\ProTiming;

$opts = getopt(null, ["url:"]);
if (!array_key_exists('url', $opts) || empty($opts['url'])){
    die("No param `--url` specified");
}

$url = $opts['url'];
if (false === filter_var($url, FILTER_VALIDATE_URL)){
    die("Invalid url.");
}

$headers = @get_headers($url);
if (empty($headers)){
    die("Unknow host.");
}
if ('200' !== ($code = substr($headers[0], 9, 3))){
    die("HTTP response code is " . $code);
}

$proTiming = new ProTiming($url);
$fileName = $proTiming->extract();

die("Filename out/$fileName created!");
