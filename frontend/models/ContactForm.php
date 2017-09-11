<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return bool whether the email was sent
     */
    public function sendEmail($email)
    {
//        return Yii::$app->mailer->compose()
//            ->setTo($email)
//
//            ->setFrom([Yii::$app->params['supportEmail'] => $this->name])
//            ->setSubject($this->subject)
//            ->setTextBody($this->body . ",mail from " . $this->email)
//            ->send();


//        return \Yii::$app
//            ->mailer
//            ->compose(
//                ['html' => 'contact-html', 'text' => 'contact-text'],
//                ['title' => $this->subject, 'content' => $this->body , 'from'=>$this->email]
//            )
//            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' guest'])
//            ->setTo($email)
//            ->setSubject('客人留言 ' . Yii::$app->name)
//            ->send();
//        var_dump(Yii::$app->params['adminEmail']) ;die() ;
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'contact-html', 'text' => 'contact-text'],
                ['title' => $this->subject, 'content' => $this->body , 'from'=>$this->email]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' website guest'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setReplyTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }
}
