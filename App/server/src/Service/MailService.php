<?php namespace App\Service;
use App\Auth;
use App\Exceptions\InternalErrorException;
use App\Exceptions\RequestException;
use App\Mailer;
use App\Model\MailModel;
use App\Model\UserModel;

class MailService
{

    /**
     * @param $mailModel MailModel
     *
     * @throws InternalErrorException
     */
    public function sendMail($mailModel){
        Mailer::sendMail($mailModel->getAddress(), $mailModel->getSubject(), $mailModel->getBody(), $mailModel->getPlainBody());
    }

    /**
     * @param $subject String
     * @param $body String
     * @param $staffUsers array
     */
    public function sendEmailToStaff($subject, $body, $staffUsers){
        try{
            $mail = new MailModel();
            $mail->setSubject($subject);
            $mail->setBody($body);
            $mail->setPlainBody($body);

            foreach( $$staffUsers as $user ){
                $mail->addAdress( $user['email'] );
            }
            $this->sendMail( $mail );
        }catch (RequestException $e){}
    }


    /**
     * @param $user UserModel
     *
     * @throws InternalErrorException
     */
    public function sendConfirmEmail($user){
        //Generando token de confirmación
        $token = Auth::getToken( $user->getId(), 1 );

        $mail = new MailModel();
        $mail->addAdress( $user->getEmail() );
        $mail->setSubject("Asesorías par: Confirmación de correo");
        //TODO: cambiar ruta de confirmación de email
        $mail->setBody("<h3>Asesorías par</h3> Favor de verificar su correo haciendo click en el siguiente enlace: <a href='".SERVER_URL."/auth/confirm/$token'>".SERVER_URL."/auth/confirm/$token</a> </p>");
        $mail->setPlainBody("Asesorías par, Favor de verificar su correo haciendo click en el siguiente enlace: ".SERVER_URL."/auth/confirm/$token");

        try{
            $this->sendMail( $mail );
        }catch (InternalErrorException $e){
            throw new InternalErrorException("insertUserAndStudent","Error al enviar correo de confirmación");
        }
    }

}