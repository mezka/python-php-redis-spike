<?php
require 'vendor/autoload.php';
Predis\Autoloader::register();

$rPub = new Predis\Client();
$rSub = new Predis\Client();

try {
    $rPub->connect();
    $rSub->connect();
}
catch (Predis\Network\ConnectionException $exception) {
    print("Can't connect to redis instance".PHP_EOL);
    exit(1);
}

$challenge = true;

if($challenge){
    
    $pubsub = $rSub->pubSubLoop();
    $pubsub->subscribe('python-php');

    $rPub->publish('php_python', 'CHALLENGE');

    foreach ($pubsub as $message) {
        if($message->kind == "message"){
            fullfillChallenge($message->payload);
            $pubsub->unsubscribe();
        }
    }

}else{
    $rPub->publish('php_python', 'LOGIN');
    exit(0);
}

unset($pubsub);

function fullfillChallenge($verificationCode){

    if($verificationCode == 1234){
        print("PHP: Challenge fulfilled with verification code {$verificationCode}".PHP_EOL);
        $GLOBALS['rPub']->publish('php_python', 'SUCCESS');
    }else{
        print("PHP: Challenge failed with verification code {$verificationCode}".PHP_EOL);
        $GLOBALS['rPub']->publish('php_python', 'FAILURE');
    }
}

