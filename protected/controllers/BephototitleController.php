<?php
/**
 * Backend Object Controller.
 * 
 
 * @package backend.controllers
 *
 */
class BephototitleController extends BeController
{
        public function __construct($id,$module=null)
	{
		 parent::__construct($id,$module);
                 $this->menu=array(
                     
                        array('label'=>t('Add Photo Title'), 'url'=>array('create'),'linkOptions'=>array('class'=>'btn btn-small')),
                );
		 
	}
        
        /**
	 * The function that do Create new Object
	 * 
	 */
	public function actionCreate()
	{                
		$this->render('phototitle_create');
	}
        
         /**
         * The function that do Update Object
         * 
         */
	public function actionUpdate()
	{            
              $id=isset($_GET['id']) ? (int) ($_GET['id']) : 0 ;
              $this->render('phototitle_update');
	}
	
	
        
         /**
	 * The function that do View User
	 * 
	 */
	public function actionView()
	{         
        $id=isset($_GET['id']) ? (int) ($_GET['id']) : 0 ;
		$this->render('phototitle_view');
	}
        /**
	 * The function that do Manage Object
	 * 
	 */
	public function actionAdmin()
	{                
		$this->render('phototitle_admin');
	}
        
    
	public function actionDelete($id)
	{                            
            GxcHelpers::deleteModel('Phototitle', $id);
	}
          
        
}