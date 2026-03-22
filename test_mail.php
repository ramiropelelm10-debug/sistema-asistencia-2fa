<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

Mail::raw('Test email body', function($message) {
    $message->to('ramiropelelm10@gmail.com')->subject('Test Subject');
});

echo "Email sent (logged).\n";