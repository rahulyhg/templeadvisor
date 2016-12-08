<?php

/**
 * This is the Widget for Render Blocks of a Region
 * 
 
 * @package cmswidgets
 *
 */
class BlockRenderWidget extends CWidget
{
    
    public $visible=true; 
    public $page = null;
    public $region = '0';
    public $layout_asset='';
	public $data=null;

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
        if (isset($this->page)) {
                        
			$blocks=Yii::app()->cache->get($this->page->page_id.'-'.$this->region);
			//print_r($blocks);
			if($blocks===false){				
					//var_dump($this->page->page_id.'-'.$this->region);
				   //get all blocks of current region, order by 'order'
		            $blocks = PageBlock::model()->findAll(array(
		                'condition'=>'page_id=:paramId and region=:regionId and status=:status',
		                'params'=>array(':paramId'=>$this->page->page_id, ':regionId'=>$this->region,':status'=>  ConstantDefine::PAGE_BLOCK_ACTIVE),                
		                'order'=>'block_order ASC'
		            ));
					if($blocks){
						Yii::app()->cache->set($this->page->page_id.'-'.$this->region,$blocks,300);
						 $this->workWithBlocks($blocks);
					} else {
						echo '';
					}
			} else {
				$this->workWithBlocks($blocks);
			}
                            	                
	   	 
        }
        
    }   

	public function workWithBlocks($blocks){
				foreach($blocks as $page_block) {
					$block=Yii::app()->cache->get('block-render-'.$page_block->block_id);
					if($block===false){
						$block =GxcHelpers::loadDetailModel('Block', $page_block->block_id);                                                                		
						if($block){
							Yii::app()->cache->set('block-render-'.$page_block->block_id,$block,300);
							$this->blockRender($block);
						}						
					} else {
						 $this->blockRender($block);
					}
					
				                                       	                        
				}
	}
	
	public function blockRender($block){
							$block_ini=parse_ini_file(Yii::getPathOfAlias('common.front_blocks.'.$block->type).DIRECTORY_SEPARATOR.'info.ini');                                                   
		                //Include the class            
		                Yii::import('common.front_blocks.'.$block->type.'.'.$block_ini['class']);                                        					
		                if($this->data!=null)
		                	$this->widget('common.front_blocks.'.$block->type.'.'.$block_ini['class'], array('block'=>$block,'page'=>$this->page,'layout_asset'=>$this->layout_asset,'data'=>$this->data));
						else  	     
							$this->widget('common.front_blocks.'.$block->type.'.'.$block_ini['class'], array('block'=>$block,'page'=>$this->page,'layout_asset'=>$this->layout_asset));
			
	}
    
    public static function setRenderOutput($obj){                     
            // We will render the layout based on the 
            // layout                
        	$name=(strpos($obj->id,'.')===false) ? $obj->id : substr($obj->id, strrpos($obj->id, '.' )+1);	
            $render='common.front_blocks.'.$obj->id.'.'.$name.'_block_output';
			/*Delete for optimize		if(file_exists(Yii::getPathOfAlias('common.front_layouts.'.$obj->page->layout.'.blocks').'/'.$obj->id.'_block_output.php')){                
                $render='common.front_layouts.'.$obj->page->layout.'.blocks.'.$obj->id.'_block_output';                
            }
            
			Delete for optimize */
            return $render;
        }
}
