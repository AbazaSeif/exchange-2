<?php
class SiteController extends Controller
{
    public function actionIndex($s = null)
    {
        $this->forward('/transport/i/');
    }

    public function actionDescription($id)
    {
	$transportInfo=Yii::app()->db->createCommand("SELECT * from transport where id='".$id."'")->queryRow();
        $allRatesForTransport = Yii::app()->db->createCommand()
            ->select('r.date, r.price, u.name')
            ->from('rate r')
            ->join('user u', 'r.user_id=u.id')
            ->where('r.transport_id=:id', array(':id'=>$id))
            ->order('r.date desc')
            ->queryAll()
         ;
         $this->render('item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
    }
     
    public function actionQuick() 
    { 
        $model = new QuickForm;
        $model->attributes = $_POST['QuickForm'];
        if($model->validate()) {
            $user = User::model()->findByPk($model->user); 
            $email = new TEmail;
            $email->from_email = $user->email;
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = Yii::app()->params['adminEmail'];
            $email->to_name    = 'Модератору';
            $email->subject    = '';
            $email->type = 'text/html';
            
            $email->body = "<div>
                    <p>
                      Пользоваетель " . $user->name." (" . $user->email . ") 
                      находясь в перевозке с id = ".$model->transport." обратился к модератору Биржи перевозок ЛБР 'АгроМаркет'
                      со следующим обращением:
                    </p>
                    <p>" . $model->message . "</p>
                </div>
            ";
            $email->sendMail();
        }
        
        Dialog::message('flash-success', 'Отправлено!', 'Спасибо, '.$user->name.'! Ваше письмо отправлено!');
        $this->redirect(array('transport/description/id/1'));
    }
}