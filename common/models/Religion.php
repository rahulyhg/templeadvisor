<?php


class Religion extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Page the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{religion}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		
		
		
		return array(
			array('name', 'required'),
			array('id, crby, mod_by, created, modified, status, addtohome', 'numerical', 'integerOnly'=>true),
			array('uid, name, cr_ip, mod_ip, icon_file, content', 'length', 'max'=>255),
			array('icon_file', 'file', 'allowEmpty'=>true, 'types'=>'jpg, gif, png, jpeg','maxSize'=>1024*1024*5,'on'=>'update'),
			array('id, name, uid, content', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		 return array(                    
                    'language' => array(self::BELONGS_TO, 'Language', 'lang'),
                ); 
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => t('ID'),
			'name' => t('Religion Name'),
			'content' => t('About Religion'),
			'icon_file' => t('Thumbnail Image(270x160)'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		//$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('content',$this->content,true);
                
                $sort = new CSort;
                $sort->attributes = array('id');
                $sort->defaultOrder = 'id DESC';
                

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>$sort,
			'Pagination' => array (
                  'PageSize' => Yii::app()->settings->get('system', 'page_size') 
             ),
		));
	}
        
 
	
	protected function afterDelete()
        {
             Seo::model()->deleteAll('uid = :id',array(':id'=>$this->uid)); 	
        }
	
	public static function GetName($id){      
            
			$models=self::model()->find(array('condition'=>'`id`=:id','params'=>array(':id'=>$id)));
			$items = t("None");
			
			if(count($models)>0){ $items = $models->name; } 
			return $items;
        }
		
	public static function GetThumbnail($data)
		{               
	         
		
			 		return 'http://temples.s3.amazonaws.com/religion/240x143/'.$data; 
					//return FRONT_SITE_URL.'resources/'.$data->prop_id.'/thumb/'.$data->img_url; 
		}

	
	    
    public static function GetAll($render=true){    
	           //$objects=self::model()->findAll();    
				
				$objects = self::model()->findAll(array(
    			'select'=>'t.name, t.id',
    			'group'=>'t.name',
   				'distinct'=>true
				));            
               
                $data=array(''=>t("None"));      
                
                if($objects && count($objects) > 0 ){
                    $data = CMap::mergeArray($data,CHtml::listData($objects,'id','name'));                            
                }                
                
                return $data;
        }
	
	
		
	public static function GetRegionByGuidWithLang($id){
            if($id){
                $page=self::model()->find(array(
                'condition'=>'guid=:paramId',
                'params'=>array(':paramId'=>$id)));
                if($page){
                    return $page;
                } else{
                    return '';
                }
            } else {
                return '';
            }
        }
		
	public static function GetLangUID($id, $lang ){
            if($id){
                $page=self::model()->find(array(
                'condition'=>'guid=:paramId',
                'params'=>array(':paramId'=>$id)));
                if($page){
                    return $page->uid;
                } else{
                    return '';
                }
            } else {
                return '';
            }
        }
		
	public static function GetUID($id){
            if($id){
                $page=self::model()->find(array(
                'condition'=>'guid=:paramId',
                'params'=>array(':paramId'=>$id)));
                if($page){
                    return $page->uid;
                } else{
                    return '';
                }
            } else {
                return '';
            }
        }
		
	public static function GetRegionName(){
		
            $page = self::model()->findAll(array(
    		'condition'=>'addtohome=1',
			'order'=>'id ASC',
			));    
			                    
           if($page){
                    return $page;
                } else{
                    return '';
                }
        }
        
}