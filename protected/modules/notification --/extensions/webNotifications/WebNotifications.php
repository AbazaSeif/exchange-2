<?ph
class WebNotifications extends CWidget {
	const METHOD_POLL = 'poll';
	const METHOD_PUSH = 'push';
    
	public $url;
	public $method = self::METHOD_POLL;
	public $pollInterval = 3000;
	public $websocket = array();
    
	public static function initClientScript($method=self::METHOD_POLL) 
    {
        $bu = Yii::app()->assetManager->publish(dirname(__FILE__) . '/assets/');
        
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        $cs->registerCssFile($bu . '/css/webnotification.min.css');
        $cs->registerScriptFile($bu . '/js/jquery.webnotification'.(YII_DEBUG ? '' : '.min').'.js');
        if ($method==self::METHOD_PUSH) {
                $cs->registerScriptFile($bu . '/js/sockjs-0.3'.(YII_DEBUG ? '' : '.min').'.js');
        }
        $cs->registerScriptFile($bu . '/js/main.js');
        
		return $bu;
	}

    public function run()
    {   
        $bu = self::initClientScript($this->method);
        $options = array(
            'url' => $this->url,
            'baseUrl' => $bu,
            'method' => $this->method,
            'pollInterval' => $this->pollInterval,
            'websocket' => $this->websocket,
        );
        
        //$options = CJavaScript::encode($options);
        //$script = "notificationsPoller.init({$options});";
        //Yii::app()->clientScript->registerScript(__CLASS__ . '#' . $this->id, $script, CClientScript::POS_END);
    }
}
