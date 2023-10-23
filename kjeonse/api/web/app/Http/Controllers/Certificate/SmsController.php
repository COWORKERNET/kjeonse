<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Service\UserService;

use Nurigo\Solapi\Models\Message;
use Nurigo\Solapi\Services\SolapiMessageService;

class SmsController extends Controller
{

    public SolapiMessageService $messageService;

    public function __construct() {
        $this->messageService = new SolapiMessageService(env("SOLAPI_API_KEY"), env("SOLAPI_API_SECRET_KEY"));
    }

    public function send($from, $to, $text) {
        try {
            $message = new Message();
            $message->setFrom($from)
                ->setTo($to)
                ->setText($text);

            $result = $this->messageService->send($message);

            return response()->json($result);

        } catch (Exception $exception) {
            return response()->json($exception->getMessage());
        }
    }
}
