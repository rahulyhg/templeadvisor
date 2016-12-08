<?php

/**
 * This is the Widget for Creating new Page 
 * 
 
 * @package  cmswidgets.page
 *
 */
class MaildocCreateWidget extends CWidget
{
    
    public $visible=true;   
     
 
    public function init()
    {
        
    }
 
    public function run()
    {
        if($this->visible)
        {
            $this->renderContent();
        }
    }
 
    protected function renderContent()
    {       
        $model = new Maildoc;
                        
        //If it has guid, it means this is a translated version
        $guid=isset($_GET['guid']) ? strtolower(trim($_GET['guid'])) : '';                                      
        //List of language that should exclude not to translate       
        $lang_exclude=array();        
        //List of translated versions
        $versions=array();                
        // If the guid is not empty, it means we are creating a translated version of a content
        // We will exclude the translated language and include the name of the translated content to $versions
        if($guid!=''){
                $review_object=  Maildoc::model()->with('language')->findAll('guid=:gid',array(':gid'=>$guid));
                if(count($review_object)>0){
                        foreach($review_object as $obj){
                                $lang_exclude[]=$obj->lang;
                                $versions[]=$obj->title.' - '.$obj->anchor.' - '.$obj->language->lang_desc;
                        }
                }
                $model->guid=$guid;
        }

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='maildoc-form')
        {
                echo CActiveForm::validate($model);
                Yii::app()->end();
        }
        
        // collect user input data
        if(isset($_POST['Maildoc']))
        {
                $model->attributes=$_POST['Maildoc'];   
								
				$current_time=time();
				$model->created = $current_time;
                $model->modified = $current_time;
				
                if($model->save()){           
                    
                    //Start to save the Page Block
                    user()->setFlash('success',t('The Document has been Added Successfully!'));                                                            
                    $model=new Maildoc;
                    Yii::app()->controller->redirect(array('admin'));
                }
        }                
        $this->render('cmswidgets.views.maildoc.maildoc_form_widget',array('model'=>$model,'lang_exclude'=>$lang_exclude,'versions'=>$versions));            

        
        
            
        
    }   
}
