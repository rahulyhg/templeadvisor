<?php

/**
 * This is the Widget for Managing  Settings
 * 
 
 * @package cmswidgets.settings
 *
 */
class SettingsWidget extends CWidget
{
    
    public $visible=true; 
    public $type='general';
 
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
        
        switch ($this->type){
             case 'system':
                 $this->showSystemForm();
                 break;
             default:
                 $this->showGeneralForm();
                 break;
         } 
    }   
    
    protected function showSystemForm(){
        
        $model=new SettingSystemForm;
		settings()->deleteCache();        
        //Set Value for the Settings
        $model->support_email=Yii::app()->settings->get('system', 'support_email');
        $model->page_size=Yii::app()->settings->get('system', 'page_size');
        $model->language_number=Yii::app()->settings->get('system', 'language_number');
		$model->keep_file_name_upload=Yii::app()->settings->get('system', 'keep_file_name_upload');
        
       
        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='settings-form')
        {
                echo CActiveForm::validate($model);
                Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['SettingSystemForm']))
        {
        		settings()->deleteCache();
                $model->attributes=$_POST['SettingSystemForm'];                  
                if($model->validate()){
                        foreach($model->attributes as $key=>$value){
                            Yii::app()->settings->set('system', $key, $value);
                        }                                                 
                        user()->setFlash('success',t('System Settings Updated Successfully!'));                                                                                            

                }
        }

        $this->render('cmswidgets.views.settings.settings_system_widget',array('model'=>$model));
    }
    
    protected function showGeneralForm(){
        
        $model=new SettingGeneralForm;        
        
		settings()->deleteCache();
        //Set Value for the Settings
        $model->site_name=Yii::app()->settings->get('general', 'site_name');
        $model->site_title=Yii::app()->settings->get('general', 'site_title');
        $model->site_description=Yii::app()->settings->get('general', 'site_description');					
		$model->slogan=Yii::app()->settings->get('general', 'slogan');
        $model->homepage=Yii::app()->settings->get('general', 'homepage');
        
        
        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='settings-form')
        {
                echo CActiveForm::validate($model);
                Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['SettingGeneralForm']))
        {
                $model->attributes=$_POST['SettingGeneralForm'];                  
                if($model->validate()){
                		settings()->deleteCache();
                        foreach($model->attributes as $key=>$value){
                            Yii::app()->settings->set('general', $key, $value);
                        }                                                 
                        user()->setFlash('success',t('General Settings Updated Successfully!'));                                                                                            

                }
        }

        $this->render('cmswidgets.views.settings.settings_general_widget',array('model'=>$model));
    }
}
