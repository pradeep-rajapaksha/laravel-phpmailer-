<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/*
 * Update composer.json with 
 *      "require": {
 *          "phpmailer/phpmailer": "^6.0"
 *      },
 * and run `composer update` before execute
 */
class Mailer extends Model
{
	// 
    private $mailer;

    public function __construct(PHPMailer $mailer)
    { 
    	// 
        $this->mailer = $mailer;
    }

    /**
     * Send an e-mail reminder .
     *
     * @param  Request  $request 
     * @return Response
     */
    public function mail($to, $from, $subject, $message, $cc, $bcc, $attachment, $attachname) 
    { 
        // 
        $to = $request->has('to') ? $request->to :"pradeepprasanna.rajapaksha4@gmail.com";
        $from = $request->has('from') ? $request->from :"pradeep@listudiosl.com";
        $subject = $request->has('subject') ? $request->subject :"test mail subject";
        $message = $request->has('message') ? $request->message :"This is the <b>test</b> HTML message body!";
        $cc = $request->has('cc') ? $request->cc : null;
        $bcc = $request->has('bcc') ? $request->bcc : null;
        $attachment = $request->has('attachment') ? $request->attachment : null;
        $attachname = $request->has('attachname') ? $request->attachname : null;
        // 
        return $this->__send_email( $to, $subject, $message, $from, $cc, $bcc, $attachment, $attachname );
    }

    /**
     * Config PHPMailer settings .
     *
     * @param  
     * @return 
     */
    private function __config_mail()
    {
        // re-init mailer if not initiated
        if ( empty($this->mailer) ) { $this->mailer = new PHPMailer; }

        //Server settings
        // Enable verbose debug output
            // $this->mailer->SMTPDebug = 2;                                 
        // Set mailer to use SMTP
            $this->mailer->isSMTP(); 
        // Specify main and backup SMTP servers
            $this->mailer->Host = env('MAIL_HOST', 'hotmail.com'); 
        // Enable SMTP authentication
            $this->mailer->SMTPAuth = env('MAIL_SMTP_AUTH', true); 
        // SMTP username
            $this->mailer->Username = env('MAIL_USERNAME', 'user@hotmail.com'); 
        // SMTP password
            $this->mailer->Password = env('MAIL_PASSWORD', 'hotmail@psw'); 
        // Enable TLS encryption, `ssl` also accepted
            $this->mailer->SMTPSecure = env('MAIL_SMTP_SECURE', 'ssl'); 
        // TCP port to connect to
            $this->mailer->Port = env('MAIL_PORT', '465'); 
    }

    /**
     * Config PHPMailer settings .
     *
     * @param  String base64
     * @return File 
     */
    private function __base64($base64=null)
    {
        $filedata = $base64;
        // dd(explode(';', $base64, 2)[1]);
        $filedata = substr($filedata, strpos($filedata, ","));
        return base64_decode($filedata);
    }

    /**
     * Get file name 
     *
     * @param  String base64, String attachname
     * @return Srting filename
     */
    private function __filename($base64=null, $attachname=null)
    {
        $filedata = $base64;
        // 
        $filedata = str_replace('base64,', '', explode(';', $base64, 2)[1]);
        $filedata = str_replace(' ', '+', $filedata);
        $file = base64_decode($filedata);

        $mime_type = str_replace('data:', '', explode(';', $base64, 2)[0]);
        $extention = $this->__get_extention($mime_type);  
        // create file name
        $filename = !empty($attachname) ? $attachname : uniqid();
        $filename = $filename . '.' . $extention; 
        // 
        return $filename;
    }

    /**
     * Get file type 
     *
     * @param  String base64
     * @return Srting mime_type
     */
    private function __filetype($base64=null)
    {
        $filedata = $base64;
        // dd(explode(';', $base64, 2)[1]);
        $filedata = str_replace('base64,', '', explode(';', $base64, 2)[1]);
        $filedata = str_replace(' ', '+', $filedata);
        $file = base64_decode($filedata);

        $mime_type = str_replace('data:', '', explode(';', $base64, 2)[0]); 

        return $mime_type;
    }

    /**
     * Get file extention 
     *
     * @param  String mime_type
     * @return Srting 
     */
    public function __get_extention($mime_type)
    {
        $extensions = array(
                        'image/png' => 'png',
                        'image/jpeg' => 'jpeg',
                        'image/jpg' => 'jpeg',
                        'image/png' => 'png',
                        'application/pdf' => 'pdf',
                        'application/msword' => 'doc',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                        'text/xml' => 'xml',
                        'text/plain' => 'txt',
                    );
        if ( array_key_exists($mime_type, $extensions) ) { return $extensions[$mime_type]; }
        return false; 
    }

    /**
     * Send email 
     *
     * @param  String base64, String attachname
     * @return Srting filename
     */
    public function __send_email(   $to = "pradeepprasanna.rajapaksha4@gmail.com", 
                                    $subject = "test mail subject", 
                                    $message = "This is the <b>test</b> HTML message body!", 
                                    $from = "pradeep@witelsolutions.com", 
                                    $cc = null, $bcc = null, $attachment = null, $attachname = null )
    {
        // // // 
        try { 
            // 
            $this->__config_mail();

            //Recipients
            $this->mailer->setFrom($from, 'W.I Tel Emailer');
            $this->mailer->addAddress($to, 'Emailer User');     // Add a recipient
            // $this->mailer->addAddress('ellen@example.com');               // Name is optional
            // $this->mailer->addReplyTo('info@example.com', 'Information');
            
            if (!empty($cc)) { 
                if (explode(';', $cc)) {
                    $cc = explode(';', $cc);
                    foreach ($cc as $key => $email) {
                        if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
                            $this->mailer->addCC($email);
                    }
                }
                else {
                    $this->mailer->addCC($cc);
                }
            }
            if (!empty($bcc)) { 
                if (explode(';', $bcc)) {
                    $bcc = explode(';', $bcc);
                    foreach ($bcc as $key => $email) {
                        if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
                            $this->mailer->addBCC($email);
                    }
                }
                else {
                    $this->mailer->addBCC($bcc);
                }
            }

            //Attachments 
            if (!empty($attachment)) { 
                $encoding = "base64";
                $this->mailer->AddStringAttachment($this->__base64($attachment), $this->__filename($attachment, $attachname), $encoding, $this->__filetype($attachment));
            } 

            //Content
            $this->mailer->isHTML(true);       // Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $message; // 'This is the HTML message body <b>in bold!</b>';
            // $this->mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';
            
            // echo '</br> Message has been sent'; 
            if ($this->mailer->send()) 
                { return response()->json([], Response::HTTP_OK); } 
            else 
                { return response()->json([$this->mailer->ErrorInfo], 503); } 
        }
        catch (Exception $e) {
            // echo 'Message could not be sent.';
            // echo 'Mailer Error: ' . $this->mailer->ErrorInfo;
            return response()->json([$this->mailer->ErrorInfo], 503);
        }
    }

}