<?php

/**
 * This is the Widget for Creating new Page 
 * 
 
 * @package  cmswidgets.page
 *
 */
class ProductCreateWidget extends CWidget
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
        
		$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
		
		$mseo = new Seo;
		
		
		if(isset($category) && ( $category == 1000 ) ) {
		
		$model = new Productcds;		
		
		$this->performAjaxValidation(array($model, $mseo));
		
        if(isset($_POST['Productcds'], $_POST['Seo']))
        {
                $model->attributes=$_POST['Productcds'];  
				$mseo->attributes=$_POST['Seo'];  
				$model->category = $category;
				$current_time = time();
				$mseo->created = $model->created = $current_time;  
				$mseo->cr_ip = $model->cr_ip = ip();
				$mseo->crby = $model->crby = Yii::app()->user->getId();                 
				$mseo->uid = $model->uid = uniqid();
				$mseo->layout = 'product';
				
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
					
					$img6->resize(870,468);
					$img6->save(RESOURCES_FOLDER.'/870x468_'.$temp_name);
					
					
					if (Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/270x160_'.$temp_name, AWS_S3_BUCKET, 'products/cds/270x160/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) )) 						
						{  
							Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/'.$temp_name, AWS_S3_BUCKET, 'products/cds/orginal/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
							
							Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/127x75_'.$temp_name, AWS_S3_BUCKET, 'products/cds/127x75/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/210x124_'.$temp_name, AWS_S3_BUCKET, 'products/cds/210x124/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/150x89_'.$temp_name, AWS_S3_BUCKET, 'products/cds/150x89/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
				
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/240x143_'.$temp_name, AWS_S3_BUCKET, 'products/cds/240x143/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/870x468_'.$temp_name, AWS_S3_BUCKET, 'products/cds/870x468/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
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
				
				$modelEdit =  GxcHelpers::loadDetailModel('Productcds', $model->id);    
				
				$modelEdit->pcode = '0004'.$category.$modelEdit->id;
				if( $modelEdit->save()) {
                    //Start to save the Page Block
                    user()->setFlash('success',t('The CD has been Added Successfully!'));                                
                    $model=new Product;
                    Yii::app()->controller->redirect(array('admin'));
                } } }
        }    
		      
        $this->render('cmswidgets.views.product.productcds_form_widget',array('model'=>$model,'mseo'=>$mseo,'category'=>$category));
		}
		
		if(isset($category) && ( $category == 1001 ) ) {
		
		$model = new Productbooks;		
		
		$this->performAjaxValidation(array($model, $mseo));
		
        // collect user input data
        if(isset($_POST['Productbooks'], $_POST['Seo']))
        {
                $model->attributes=$_POST['Productbooks'];  
				$mseo->attributes=$_POST['Seo'];  
				$model->category = $category;
				$current_time = time();
				$mseo->created = $model->created = $current_time;  
				$mseo->cr_ip = $model->cr_ip = ip();
				$mseo->crby = $model->crby = Yii::app()->user->getId();                 
				$mseo->uid = $model->uid = uniqid();
				$mseo->layout = 'product';
				
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
					
					$img6->resize(870,468);
					$img6->save(RESOURCES_FOLDER.'/870x468_'.$temp_name);
					
					
					if (Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/270x160_'.$temp_name, AWS_S3_BUCKET, 'products/books/270x160/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) )) 						
						{  
						
						Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/'.$temp_name, AWS_S3_BUCKET, 'products/books/orginal/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
						
							Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/127x75_'.$temp_name, AWS_S3_BUCKET, 'products/books/127x75/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/210x124_'.$temp_name, AWS_S3_BUCKET, 'products/books/210x124/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/150x89_'.$temp_name, AWS_S3_BUCKET, 'products/books/150x89/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
				
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/240x143_'.$temp_name, AWS_S3_BUCKET, 'products/books/240x143/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/870x468_'.$temp_name, AWS_S3_BUCKET, 'products/books/870x468/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
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
				
				$modelEdit =  GxcHelpers::loadDetailModel('Productbooks', $model->id);    
				
				$modelEdit->pcode = '0004'.$category.$modelEdit->id;
				if( $modelEdit->save()) {
                    //Start to save the Page Block
                    user()->setFlash('success',t('The Book Added Successfully!'));                                
                    $model=new Product;
                    Yii::app()->controller->redirect(array('admin'));
                } } }
        }    
		      
        $this->render('cmswidgets.views.product.productbooks_form_widget',array('model'=>$model,'mseo'=>$mseo,'category'=>$category));
		}
		
		if(isset($category) && ( $category == 3006 ) ) {
		
		$model = new Productpoojaitems;		
		
		$this->performAjaxValidation(array($model, $mseo));
		
        // collect user input data
        if(isset($_POST['Productpoojaitems'], $_POST['Seo']))
        {
                $model->attributes=$_POST['Productpoojaitems'];  
				$mseo->attributes=$_POST['Seo'];  
				$model->category = $category;
				$current_time = time();
				$mseo->created = $model->created = $current_time;  
				$mseo->cr_ip = $model->cr_ip = ip();
				$mseo->crby = $model->crby = Yii::app()->user->getId();                 
				$mseo->uid = $model->uid = uniqid();
				$mseo->layout = 'product';
				
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
					
					$img6->resize(870,468);
					$img6->save(RESOURCES_FOLDER.'/870x468_'.$temp_name);
					
					
					if (Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/270x160_'.$temp_name, AWS_S3_BUCKET, 'products/poojaitems/270x160/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) )) 						
						{  
						
						Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/'.$temp_name, AWS_S3_BUCKET, 'products/poojaitems/orginal/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
						
						
							Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/127x75_'.$temp_name, AWS_S3_BUCKET, 'products/poojaitems/127x75/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/210x124_'.$temp_name, AWS_S3_BUCKET, 'products/poojaitems/210x124/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/150x89_'.$temp_name, AWS_S3_BUCKET, 'products/poojaitems/150x89/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
				
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/240x143_'.$temp_name, AWS_S3_BUCKET, 'products/poojaitems/240x143/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/870x468_'.$temp_name, AWS_S3_BUCKET, 'products/poojaitems/870x468/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
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
				
				$modelEdit =  GxcHelpers::loadDetailModel('Productpoojaitems', $model->id);    
				
				$modelEdit->pcode = '0004'.$category.$modelEdit->id;
				if( $modelEdit->save()) {
                    //Start to save the Page Block
                    user()->setFlash('success',t('The Pooja Items Added Successfully!'));                                
                    $model=new Product;
                    Yii::app()->controller->redirect(array('admin'));
                } } }
        }    
		      
        $this->render('cmswidgets.views.product.productpoojaitems_form_widget',array('model'=>$model,'mseo'=>$mseo,'category'=>$category));
		}
		
		if(isset($category) && ( $category == 3007 ) ) {
		
		$model = new Productidols;		
		
		$this->performAjaxValidation(array($model, $mseo));
		
        // collect user input data
        if(isset($_POST['Productidols'], $_POST['Seo']))
        {
                $model->attributes=$_POST['Productidols'];  
				$mseo->attributes=$_POST['Seo'];  
				$model->category = $category;
				$current_time = time();
				$mseo->created = $model->created = $current_time;  
				$mseo->cr_ip = $model->cr_ip = ip();
				$mseo->crby = $model->crby = Yii::app()->user->getId();                 
				$mseo->uid = $model->uid = uniqid();
				$mseo->layout = 'product';
				
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
					
					$img6->resize(870,468);
					$img6->save(RESOURCES_FOLDER.'/870x468_'.$temp_name);
					
					
					if (Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/270x160_'.$temp_name, AWS_S3_BUCKET, 'products/idols/270x160/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) )) 						
						{  
						
						Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/'.$temp_name, AWS_S3_BUCKET, 'products/idols/orginal/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
						
							Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/127x75_'.$temp_name, AWS_S3_BUCKET, 'products/idols/127x75/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/210x124_'.$temp_name, AWS_S3_BUCKET, 'products/idols/210x124/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/150x89_'.$temp_name, AWS_S3_BUCKET, 'products/idols/150x89/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
				
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/240x143_'.$temp_name, AWS_S3_BUCKET, 'products/idols/240x143/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/870x468_'.$temp_name, AWS_S3_BUCKET, 'products/idols/870x468/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
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
				
				$modelEdit =  GxcHelpers::loadDetailModel('Productidols', $model->id);    
				
				$modelEdit->pcode = '0004'.$category.$modelEdit->id;
				if( $modelEdit->save()) {
                    //Start to save the Page Block
                    user()->setFlash('success',t('The Idols Added Successfully!'));                                
                    $model=new Product;
                    Yii::app()->controller->redirect(array('admin'));
                } } }
        }    
		      
        $this->render('cmswidgets.views.product.productidols_form_widget',array('model'=>$model,'mseo'=>$mseo,'category'=>$category));
		}
		
		if(isset($category) && ( $category == 3008 ) ) {
		
		$model = new Productotheritems;		
		
		$this->performAjaxValidation(array($model, $mseo));
		
        // collect user input data
        if(isset($_POST['Productotheritems'], $_POST['Seo']))
        {
                $model->attributes=$_POST['Productotheritems'];  
				$mseo->attributes=$_POST['Seo'];  
				$model->category = $category;
				$current_time = time();
				$mseo->created = $model->created = $current_time;  
				$mseo->cr_ip = $model->cr_ip = ip();
				$mseo->crby = $model->crby = Yii::app()->user->getId();                 
				$mseo->uid = $model->uid = uniqid();
				$mseo->layout = 'product';
				
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
					
					$img6->resize(870,468);
					$img6->save(RESOURCES_FOLDER.'/870x468_'.$temp_name);
					
					
					if (Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/270x160_'.$temp_name, AWS_S3_BUCKET, 'products/otheritems/270x160/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) )) 						
						{  
						
						Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/'.$temp_name, AWS_S3_BUCKET, 'products/otheritems/orginal/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
						
							Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/127x75_'.$temp_name, AWS_S3_BUCKET, 'products/otheritems/127x75/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/210x124_'.$temp_name, AWS_S3_BUCKET, 'products/otheritems/210x124/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					 
					 		Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/150x89_'.$temp_name, AWS_S3_BUCKET, 'products/otheritems/150x89/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
				
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/240x143_'.$temp_name, AWS_S3_BUCKET, 'products/otheritems/240x143/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
					 "Cache-Control" => "max-age=94608000", "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+3 years"))) );
					
					 Yii::app()->s3->putObjectFile(RESOURCES_FOLDER.'/870x468_'.$temp_name, AWS_S3_BUCKET, 'products/otheritems/870x468/'.$temp_name, S3::ACL_PUBLIC_READ, $metaHeaders = array(
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
				
				$modelEdit =  GxcHelpers::loadDetailModel('Productotheritems', $model->id);    
				
				$modelEdit->pcode = '0004'.$category.$modelEdit->id;
				if( $modelEdit->save()) {
                    //Start to save the Page Block
                    user()->setFlash('success',t('Other Items Added Successfully!'));                                
                    $model=new Product;
                    Yii::app()->controller->redirect(array('admin'));
                } } }
        }    
		      
        $this->render('cmswidgets.views.product.productotheritems_form_widget',array('model'=>$model,'mseo'=>$mseo,'category'=>$category));
		}
		
		
    } 
	
	protected function performAjaxValidation($models)
	{
    	if(isset($_POST['ajax']) && $_POST['ajax']==='product-form')
        {
                echo CActiveForm::validate($models);
                Yii::app()->end();
        }
	} 
}
