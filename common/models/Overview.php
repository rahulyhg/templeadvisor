<?php


class Overview extends CActiveRecord
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
		return '{{overview}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		
		
		
		return array(
			array('status, title , description, uid', 'required'),
			array('id, crby, created, modified, mod_by, status', 'numerical', 'integerOnly'=>true),
			array('uid, cr_ip, mod_ip', 'length', 'max'=>255),
			array('id, uid, title', 'safe', 'on'=>'search'),
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
			'title' => t('Overview Title'),
			'description' => t('Overview Description'),
			'status'=> t('Status'),
			'addtohome' => t('Add to Quick List On Home'),
			
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

		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		
		
                $sort = new CSort;
                $sort->attributes = array(
                        'id',
                );
                $sort->defaultOrder = 'id DESC';
                

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>$sort,
			'Pagination' => array (
                  'PageSize' => Yii::app()->settings->get('system', 'page_size') 
             ),
		));
	}
        
       
	
	public static function GetName($id){    
		                       
               
            $models=self::model()->find(array(
		    'condition'=>'`id`=:id',
		    'params'=>array(':id'=>$id)));
			
			$items = t("None");
			
			if(count($models)>0){                
                
                        $items = $models->title;
                
	    	} 
			return $items;
        }

	
	public static function GetAllType(){    
	           //$objects=self::model()->findAll();    
				
				$objects = self::model()->findAll(array(
				'condition'=>'status = 1',
				));                  
                
                if($objects && count($objects) > 0 ){
                    return $objects;                          
                }                
                
        }
	
	 
	 public static function GetUID($id){
            if($id){
                $page=self::model()->findByPk($id);
                if($page){
                    return $page->uid;
                } else{
                    return '';
                }
            } else {
                return '';
            }
        }
	 
	 
	    
    public static function GetAll($render=true){    
	           //$objects=self::model()->findAll();    
				
				$objects = self::model()->findAll(array(
    			'select'=>'t.title, t.id',
    			'group'=>'t.title',
   				
				));            
               
                $data=array(''=>t("None"));      
                
                if($objects && count($objects) > 0 ){
                    $data = CMap::mergeArray($data,CHtml::listData($objects,'id','title'));                            
                }                
                
                return $data;
        }
        
}