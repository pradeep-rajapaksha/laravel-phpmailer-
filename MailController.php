<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use App\Mailer;

/*
 * Update composer.json with 
 *      "require": {
 *          "phpmailer/phpmailer": "^6.0"
 *      },
 * and run `composer update` before execute
 */
class MailController extends Controller
{

    private $mailer;

    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send an e-mail.
     *
     * @param  Request  $request 
     * @return Response
     */
    public function mail(Request $request)
    { 
        // 
        $to = $request->has('to') ? $request->to :"dev.pradeepr@gmail.com";
        $from = $request->has('from') ? $request->from :"pradeep@listudiosl.com";
        $subject = $request->has('subject') ? $request->subject :"test mail subject";
        $message = $request->has('message') ? $request->message :"This is the <b>test</b> HTML message body!";
        $cc = $request->has('cc') ? $request->cc : null;
        $bcc = $request->has('bcc') ? $request->bcc : null;
        $attachment = $request->has('attachment') ? $request->attachment : null;
        $attachname = $request->has('attachname') ? $request->attachname : null;
        // 
        return Mailer::mail( $to, $subject, $message, $from, $cc, $bcc, $attachment, $attachname );
    }
}