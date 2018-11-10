<?php
// Loading the json config
$string = file_get_contents("config.json");

if ($string === false){
    mylog("config.json not found");
    return true;
}
$config = json_decode($string, true);

// I will just create config node clone for each "Label" entry I will find
// This is just for convenience
foreach ($config['devices'] as $key => $device) {
    if(isset($device['labels']) && $device['labels']){
        $labels_array = explode(",",$device['labels']);
        foreach ($labels_array as $label) {
            $label = sanitize($label);
            if (!isset($config['devices'][$label])){
                unset($device['labels']);
                $config['devices'][$label] = $device;
            }
        }
    }
}

// Check if the GET request is valid vs the json config
if (!isset($_GET['wake'])){
    mylog("Wake GET parameter not found");
    return true;
}
if (strlen($_GET['wake']) == 0){
    mylog("Wake GET parameter is empty");
    return true;
}
$_GET['wake'] = sanitize($_GET['wake']);
if (!isset($config['devices'][$_GET['wake']])){
    mylog("Device not found inside the config.json");
    return true;
}

// At this point the value we have, have a match in our config
$config_loaded = $config['devices'][$_GET['wake']];

try {
    mylog("New accepted wake request for: {$_GET['wake']}");
    $ip = gethostbyname($config_loaded['public-ip']);
    mylog("IP: {$ip}");
    $mac = $config_loaded['mac-address'];
    mylog("MAC: {$mac}");
    $port = $config_loaded['port'];
    mylog("PORT: {$port}");
    $result = shell_exec("/bin/bash wol.bash \"{$mac}\" \"{$ip}\" \"{$port}\"");
} catch(Exception $e) {
    $result = $e->getMessage();
}

// $result empty then all went well, I think.
if (!$result){
    $result = "Done!";
}

mylog($result);

function mylog($custom_message,$log_filename = null)
{
    $current_date = date('Y-m-d H:i:s');

    if(!$log_filename){
        $log_filename = "wol.log";
    }

    if (!file_exists($log_filename)) {
        $myfile = fopen($log_filename, "w");
        fclose($myfile);
    }

    if (is_array($custom_message)){
        $custom_message = print_r($custom_message,true);
    }

    file_put_contents($log_filename, $current_date . " |--| " . $custom_message . PHP_EOL, FILE_APPEND);
    return true;
}


// I'm using this to clean a string and keeping only letter, number and spaces lowercase
function sanitize($string){
    $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
        'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
        'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $string = strtr( $string, $unwanted_array );
    $string = preg_replace("/[^ a-zA-Z0-9]+/", "", $string);
    $string = preg_replace('/\s+/', ' ',$string);
    $string = trim(strtolower($string));
    return $string;
}

return true;

