<?php

/**
 * Class for handle Upload to Local Storage
 * 
 * 
 
 * @package common.storages
 */

 //This is a must Have Resource Class - Don't Delete this
class LocalStorage
{
	  
	  public $max_file_size=ConstantDefine::UPLOAD_MAX_SIZE;
	  public $min_file_size=ConstantDefine::UPLOAD_MIN_SIZE;
	  public $allow_types=array();
	  
	  public function __construct($max_file_size=ConstantDefine::UPLOAD_MAX_SIZE,$min_file_size=ConstantDefine::UPLOAD_MIN_SIZE,$allow_types=array()) {
	  		$this->max_file_size=$max_file_size;
			$this->min_file_size=$min_file_size;
			$this->allow_types=$allow_types;
	  }
	  	
	  public function UploadFile(&$resource,$model,&$process,&$message,$remote=false){
	  			
				
			if($model->upload->size > $this->max_file_size){
				$allow_size=$this->max_file_size/(1024*1024);							
				$model->addError('upload', t('File size is larger than allowed size : ').$allow_size. ' mb');
				$process=false;
				return false;
			}  
			
			if($model->upload->size < $this->min_file_size){
				$model->addError('upload', t('File is too small!'));
				$process=false;
				return false;
			} 
			
			if(count($this->allow_types)>0){
				if(!in_array(strtolower(CFileHelper::getExtension($model->upload->name)), $this->allow_types)){
					$model->addError('upload', t('File extension is not allowed!'));
					$process=false;
					return false;
				}
			}
	  		$filename=$resource->resource_name=$model->upload->name;			
			if(settings()->get('system','keep_file_name_upload')=='0'){				
				$filename=gen_uuid();	
			} else {
				$filename=str_replace(" ","-",$filename) ;
			}
			
			// folder for uploaded files
			$folder=date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR; 
			if (!(file_exists(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder) && 
				(is_dir(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder)))){
				mkdir(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder,0777,true);
			}
			
			if(settings()->get('system','keep_file_name_upload')=='1'){
				//Check if File exists, so Rename the Filename again;
				 while (file_exists(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$filename.'.'
				 .strtolower(CFileHelper::getExtension($model->upload->name)))) {
				     $filename .= rand(10, 99);
				 }
			}			
			$filename=$filename.'.'.strtolower(CFileHelper::getExtension($model->upload->name));
			$path=$folder.$filename;
			if($model->upload->saveAs(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$path)){				
			    $resource->resource_path=$path;			    
			    //Resource::generateThumb($model->upload->name,$folder,$filename);			    			    
			    $process=true;
			    return true;
			} else {
			    $process=false;
			    $message=t('Error while Uploading. Try again later.');
			    return false;
			  
			}
	  }

	  public function getRemoteFile(&$resource,$model,&$process,&$message,$path,$ext,$changeresname=true){
			
			if(count($this->allow_types)>0){
				if(!in_array(strtolower($ext), $this->allow_types)){
					$message=t('File extension is not allowed!');
					$process=false;
					return false;
				}
			}
				  				  			
	  		$ch = curl_init($path);
			curl_setopt($ch,CURLOPT_HEADER,0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			$rawdata=curl_exec($ch);
			curl_close($ch);
			
			if(!$rawdata) {
				$process=false;
				$message=t('Error while getting Remote File. Try again later.');
				return false;
			}
			
						
			$filename=gen_uuid();
			
			// folder for uploaded files
			$folder=date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR; 
			if (!(file_exists(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder) && 
				(is_dir(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder)))){
				mkdir(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder,0777,true);
			}
			
			
			//Check if File exists, so Rename the Filename again;
			 while (file_exists(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$filename.'.'
			 .strtolower($ext))) {
			     $filename .= rand(10, 99);
			 }
			
			
			$filename=$filename.'.'.$ext;
			
			$path=$folder.$filename;
			$fullpath=RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$path;			
			
			$fp = fopen($fullpath,'x');
			fwrite($fp, $rawdata);
			fclose($fp);
						
			$resource->where=$model->where;					
			$resource->resource_path=$path;
			
			if($changeresname){
				$resource->resource_name=$filename;
			}
			
			//Resource::generateThumb($filename,$folder,$filename);
			$process=true;
			return true;
	  }
	  
	  public function getFilePath($file){
	  		return RESOURCE_URL.'/'.$file;
	  }
	  
	  public function deleteResource($resource){
	  		  		
	  		if(file_exists(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$resource->resource_path)){
	  			unlink(RESOURCES_FOLDER.DIRECTORY_SEPARATOR.$resource->resource_path);
	  		}
			return;
	  }
	   	
}
