<?php

/**
 * This is the Widget for Updating a Page 
 * 
 
 * @package  cmswidgets.page
 *
 */
class HomamlistUpdateWidget extends CWidget
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
        $id=isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $model=  GxcHelpers::loadDetailModel('Homamlist', $id);
		
			$mseo = Seo::model()->find(array(
                'condition'=>'uid=:obj',
                'params'=>array(':obj'=>$model->uid)
            ));
			
			
			$this->performAjaxValidation(array($model, $mseo));
            
      
      
      
        // collect user input data
        if(isset($_POST['Homamlist'], $_POST['Seo']))
        {
                $model->attributes=$_POST['Homamlist'];    
				$mseo->attributes=$_POST['Seo'];     
				$current_time=time();
                $mseo->modified = $model->modified = $current_time;    
				$mseo->mod_ip = $model->mod_ip = ip();    
				$mseo->mod_by = $model->mod_by = Yii::app()->user->getId();  
				
				 if(isset($model->icon_file)) {  
				$IconFile = CUploadedFile::getInstance($model,'icon_file');
				
				
				if( isset($IconFile) ) {
					
					$temp_name = toSlug($model->name).'_'.time().'.'.$IconFile->getExtensionName();
					$IconFile->saveAs(RESOURCES_FOLDER.'/'.$temp_name);
					
					$img1 = Yii::app()->simpleImage->load(RESOURCES_FOLDER.'/'.$temp_name);
					$img2 = Yii::app()->simpleImage->load(RESOURCES_FOLDER.'/'.$temp_name);
					$img3 = Yii::app()->simpleImage->load(RESOURCES_FOLDER.'/'.$temp_name);
					$img4 = Yii::app()->simpleImage->load(RESOURCES_FOLDER.'/'.$temp_name);
					$img5 = Yii::app()->simpleImage->load(RESOURCES_FOLDER.'/'.$temp_name);
					$img6 = Yii::app()->simpleImage->load(RESOURCES_FOLDER.'/'.$temp_name);
					
					$img1->resize(270,160);
					$img1->save(RESOURCES_FOLDER.'/270x160_'.$temp_name);
					
					$img2->resize(127,75);
					$img2->save(RESOURCES_FOLDER.'/127x75_'.$temp_name);
					
					$img3->resize(210,124);
					$img3->save(RESOURCES_FOLDER.'/210x124_'.$temp_name);
					
					$img4->resize(150,89);
					$img4->save(RESOURCES_FOLDER.'/150x89_'.$temp_name);
					
					$img5->resize(240,143);
					$img5->save(RESOURCES_FOLDER.'/240x143_'.$temp_name);
					
					$img6->resize(600,400);
					$img6->save(RESOURCES_FOLDER.'/600x400_'.$temp_name);
					
					
					if (Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/270x160_'.$temp_name, AWS_S3_BUCKET, 'homam/list/270x160/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) )) 						
						{  
							Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/127x75_'.$temp_name, AWS_S3_BUCKET, 'homam/list/127x75/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/210x124_'.$temp_name, AWS_S3_BUCKET, 'homam/list/210x124/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/150x89_'.$temp_name, AWS_S3_BUCKET, 'homam/list/150x89/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
				
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/240x143_'.$temp_name, AWS_S3_BUCKET, 'homam/list/240x143/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/600x400_'.$temp_name, AWS_S3_BUCKET, 'homam/list/600x400/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					
					
					$model->icon_file =   $temp_name;
    				unlink(RESOURCES_FOLDER.'/'.$temp_name);
					
				} }
				}  
				
				$valid=$model->validate();
				$valid=$mseo->validate() && $valid;
				
				
                                       
                 if($valid)
        			{
				         
                if($model->save() && $mseo->save()){       
                    user()->setFlash('success',t('Update Homam Successfully!'));  
					Yii::app()->controller->redirect(array('create'));                                                                            
                } }
        }
		
		          
        $this->render('cmswidgets.views.homamlist.homamlist_form_widget',array('model'=>$model,'mseo'=>$mseo));            
    }   
	
	protected function performAjaxValidation($models)
	{
    	if(isset($_POST['ajax']) && $_POST['ajax']==='homamlist-form')
        {
                echo CActiveForm::validate($models);
                Yii::app()->end();
        }
	}   
}
