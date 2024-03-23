<?php
header('Content-Type: text/html; charset=utf-8');

$input = file_get_contents('php://input');
$update = json_decode($input);
$message = $update->message;
$chat_id = $message->chat->id;
$text = $message->text;
$apiToken = "API token"; // Replace with your actual API token


function sendMessage($chat_id, $text, $apiToken) {
    $url = "https://api.telegram.org/bot$apiToken/sendMessage";
    $data = array('chat_id' => $chat_id, 'text' => $text);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

if ($text == '/send_acme_challenge') {

    exec('sudo certbot certificates', $output, $returnCode);
    if ($returnCode === 0) {
        $acmeChallengeData = implode("\n", $output);

        sendMessage($chat_id, "ACME Challenge Data: \n$acmeChallengeData", $apiToken);
    } else {
        sendMessage($chat_id, "Failed to obtain ACME challenge data.", $apiToken);
    }
}


if (strpos($text, '/request') !== false || strpos($text, '/ok') !== false) {

    $parts = explode(' ', $text);
    $domain = isset($parts[1]) ? $parts[1] : '';


    $descriptorspec = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
    );
    $process = proc_open("sudo certbot certonly --manual --preferred-challenges dns -d $domain", $descriptorspec, $pipes);

    if (is_resource($process)) {

        fwrite($pipes[0], "\n");
        fclose($pipes[0]);


        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);


        $return_value = proc_close($process);

        sendMessage($chat_id, "Certificate request result for domain $domain: \n$output", $apiToken);
    } else {
        sendMessage($chat_id, "Failed to execute Certbot command.", $apiToken);
    }
}










   if (strpos($text, '/dns') !== false) {

    $parts = explode(' ', $text);
    $domain = isset($parts[1]) ? $parts[1] : '';


    exec("sudo certbot certonly --manual --preferred-challenges dns -d $domain 2>&1", $output);


    $filteredOutput = [];
    $foundChallenge = false;

    foreach ($output as $index => $line) {

        if ($foundChallenge && strpos($line, 'with the following value:') !== false) {

            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = 'Value=';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = isset($output[$index + 2]) ? $output[$index + 2] : '';


            break;
        }


        if (strpos($line, '_acme-challenge') !== false) {

            $filteredOutput[] = 'DNS INFO';
            $filteredOutput[] = 'TYPE=TXT';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = 'NAME=';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = $line;
            $foundChallenge = true;
        }
    }


    $outputText = implode("\n", $filteredOutput);


    sendMessage($chat_id, " \n$outputText", $apiToken);
}


   if (strpos($text, '/start') !== false) {



    $filteredOutput = [];
    $foundChallenge = false;

            $filteredOutput[] = 'Welcome !!';
            $filteredOutput[] = '';
            $filteredOutput[] = '1-    /dns example.com';
            $filteredOutput[] = 'You will see the TXT record information';
            $filteredOutput[] = 'Edit your domain information';
            $filteredOutput[] = 'Wait two minutes and move on to the next step';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = '2-    /request example.com';
            $filteredOutput[] = 'Now the server creates an SSL certificate';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $filteredOutput[] = '';
            $foundChallenge = true;


    $outputText = implode("\n", $filteredOutput);


    sendMessage($chat_id, " \n$outputText", $apiToken);
        }







if ($text == '/file') {

    $file_paths = array(
        '/var/www/html/ssl/fullchain.pem',
        '/var/www/html/ssl/privkey.pem'
    );


    $missing_files = array();
    foreach ($file_paths as $path) {
        if (!file_exists($path)) {
            $missing_files[] = basename($path);
        }
    }

    if (!empty($missing_files)) {

        sendMessage($chat_id, "The following files do not exist: " . implode(', ', $missing_files), $apiToken);
    } else {

        sendFiles($chat_id, $file_paths, $apiToken);
    }
}


function sendFiles($chat_id, $file_paths, $apiToken) {
    $url = "https://api.telegram.org/bot$apiToken/sendDocument";

    foreach ($file_paths as $file_path) {

        $ch = curl_init();


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);


        $post_fields = array(
            'chat_id' => $chat_id,
            'document' => new CURLFile(realpath($file_path))
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);


        $output = curl_exec($ch);


        curl_close($ch);
    }
}









                     


?>
