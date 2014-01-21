<?php
class SiteController extends Controller
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($s = null)
	{
		$this->forward('transport/index/');
	}
}