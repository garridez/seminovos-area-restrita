<?php

if ($_POST['subscription']) {
    file_put_contents('push-subscriptions.txt', $_POST['subscription'] . PHP_EOL);
}