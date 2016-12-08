<?php


class Feedlist extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Page the static model class
	 */
	public $group_label;
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{feedlist}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		
		
		
		return array(
			array('created, modified, status, name, sharevia, partner_ip, secret_key', 'required'),
			array('id, crby, mod_by, created, modified, status, sharevia', 'numerical', 'integerOnly'=>true),
			array('uid, name, person_name, website, cr_ip, mod_ip, email_id', 'length', 'max'=>255),
			array('content, feed_url', 'length', 'min'=>25),
			array('id, name, uid', 'safe', 'on'=>'search'),
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
			'name' => t('Portal Name'),
			'person_name' => t('Tech people to contact'),
			'email_id' => t('Contact Info, email, skype, IM'),
			'website' => t('Web Site URL'),
			'sharevia' => t('Share via API/FEED'),
			'content' => t('Sample Feed'),
			'feed_url' => t('Feed URL to be Share'),
			'partner_ip' => t('Partner IP'),
			'secret_key' => t('Secret Key'),
			'crby'=>t('Created By'),			
			'created' => t('Create Date'),
			'group_label' => t('Group')
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
		$criteria->compare('sharevia',$this->sharevia,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('created',$this->created,true);
                
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
                
                        $items = $models->name;
                
	    	} 
			return $items;
        }

	
	    
    public static function GetAll($render=true){    
	           //$objects=self::model()->findAll();    
				
				$objects = self::model()->findAll(array(
    			'select'=>'t.name, t.id',
    			'group'=>'t.name',
   				'distinct'=>true,
				));            
               
                $data=array(''=>t("None"));      
                
                if($objects && count($objects) > 0 ){
                    $data = CMap::mergeArray($data,CHtml::listData($objects,'id','name'));                            
                }                
                
                return $data;
        }
     public static function GetGroupAll($render=true){    
	           //$objects=self::model()->findAll();    
				
				$objects = self::model()->findAll(array(
    			'select'=>'t.name, CONCAT("FP-",t.id) as id',
    			'group'=>'t.name',
   				'distinct'=>true,
				));            
               
                $data=array(''=>t("None"));      
                
                if($objects && count($objects) > 0 ){
                    //$data = CMap::mergeArray($data,CHtml::listData($objects,'id','name')); 
					$data = array(t("None")=>$data,'Feed Partners'=>CHtml::listData($objects,'id','name'),'Travel Agent'=>Villaowner::GetTaAll());                               
                }                
                
                return $data;
        }
       
}