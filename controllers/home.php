<?php
class home
{
	public function default_action()
	{
		$this->view->var_test = "Variable de template !";
		Debug::show('Action par défaut');
	}
	
	public function action2()
	{
		Debug::show('Action 2');
	}
}
?>