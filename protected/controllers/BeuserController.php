<?php
/**
 * Backend User Controller.
 * 
 
 * @package backend.controllers
 *
 */
class BeuserController extends BeController{
    
       
        public function __construct($id,$module=null)
	{
		 parent::__construct($id,$module);
                 $this->menu=array(
                        array('label'=>t('Manage User'), 'url'=>array('admin'),'linkOptions'=>array('class'=>'btn btn-mini'),'visible'=>((user()->isAgent) || (user()->isVillaOwner))?false:true),
                        array('label'=>t('Create User'), 'url'=>array('create'),'linkOptions'=>array('class'=>'btn btn-mini'),'visible'=>((user()->isAgent) || (user()->isVillaOwner))?false:true),
                );
		 
	}
         /**
	 * The function that do Change Password
	 * 
	 */
	public function actionChangePass()
	{                
                $this->menu=array();                        
                $this->render('user_change_pass');
	}
        
        /**
	 * The function that do Update Settings 
	 * 
	 */
	public function actionUpdateSettings()
	{        
                $this->menu=array();                        
		$this->render('user_update_settings');
	}
        
        /**
	 * The function that do Create new User
	 * 
	 */
	public function actionCreate()
	{                
		$this->render('user_create');
	}
        
        /**
	 * The function that do Manage User
	 * 
	 */
	public function actionAdmin()
	{                
		$this->render('user_admin');
	}
        
        /**
	 * The function that do View User
	 * 
	 */
	public function actionView()
	{         
              $id=isset($_GET['id']) ? (int) ($_GET['id']) : 0 ;
              $this->menu=array_merge($this->menu,                       
                        array(
                            array('label'=>t('Update this user'), 'url'=>array('update','id'=>$id),'linkOptions'=>array('class'=>'btn btn-mini'),'visible'=>((user()->isAgent) || (user()->isVillaOwner))?false:true),
                            array('label'=>t('View this user'), 'url'=>array('view','id'=>$id),'linkOptions'=>array('class'=>'btn btn-mini'),'visible'=>((user()->isAgent) || (user()->isVillaOwner))?false:true)
                        )
                    );
		$this->render('user_view');
	}
        
        /**
	 * The function that do Update User
	 * 
	 */
	public function actionUpdate()
	{                
                $id=isset($_GET['id']) ? (int) ($_GET['id']) : 0 ;
                
                $this->menu=array_merge($this->menu,                       
                        array(
                            array('label'=>t('Update this user'), 'url'=>array('update','id'=>$id),'linkOptions'=>array('class'=>'btn btn-mini')),
                            array('label'=>t('View this user'), 'url'=>array('view','id'=>$id),'linkOptions'=>array('class'=>'btn btn-mini'))
                        )
                    );
		$this->render('user_update');
	}
        
        /**
	 * The function is to Delete a User
	 * 
	 */
	public function actionDelete($id)
	{                            
            GxcHelpers::deleteModel('User', $id);
	}
	
	public function actionPeople()
	{
		$people_id = $_GET['people_id'];
		$model = Villaowner::model()->findByPk($people_id);
		echo $model->email.'//'.$model->display_name;
	}
                    
}