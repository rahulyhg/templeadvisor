<div class="workplace">
  <div class="form">
    <?php $this->render('cmswidgets.views.notification'); ?>
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'villaowner-form',
        'enableAjaxValidation'=>true,   
		'htmlOptions' => array('enctype' => 'multipart/form-data'),        
        )); 
?>
    <?php echo $form->errorSummary(array($model)); ?>
    <div class="span6" style="float:right;"> </div>
    <div class="row-fluid">
      <div class="span6">
        <div class="head">
          <div class="isw-target"></div>
          <h1><?php echo t('Address Info'); ?></h1>
          <div class="clear"></div>
        </div>
        <div class="block-fluid">
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'name'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'name'); ?> <span><?php echo $form->error($model,'name'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'display_name'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'display_name'); ?> <span><?php echo $form->error($model,'display_name'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'company'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'company'); ?> <span><?php echo $form->error($model,'company'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'address1'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'address1'); ?> <span><?php echo $form->error($model,'address1'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'address2'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'address2'); ?> <span><?php echo $form->error($model,'address2'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'town'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'town'); ?> <span><?php echo $form->error($model,'town'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'province'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'province'); ?> <span><?php echo $form->error($model,'province'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'country'); ?></div>
            <div class="span7"> <?php echo $form->dropDownList($model, 'country', Countrylist::GetAll(false), array()); ?> <span><?php echo $form->error($model,'country'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'note'); ?></div>
            <div class="span7"> <?php echo $form->textArea($model, 'note'); ?> <span><?php echo $form->error($model,'note'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form" <?php if(user()->isVillaOwner){?> style=" display:none;"<?php } ?> >
            <div class="span5"><?php echo $form->label($model,'status'); ?></div>
            <div class="span7"> <?php echo $form->dropDownList($model,'status',  ConstantDefine::getPageStatus(),array()); ?> <span><?php echo $form->error($model,'status'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form" <?php if(user()->isVillaOwner){?> style=" display:none;"<?php } ?> >
            <div class="span5"><?php echo $form->label($model,'avatar'); ?></div>
            <div class="span7"> <?php echo $form->fileField($model, 'avatar');?> <span><?php echo $form->error($model,'avatar'); ?> </span></div>
            <div class="clear"></div>
            <?php if($model->avatar!=''){?>
            <div style="height:100px;">
              <div style="float:right;"><?php echo CHtml::image(AVATAR_FOLDER.$model->avatar,'DORE'); ?></div>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="span6">
        <div class="head">
          <div class="isw-target"></div>
          <h1><?php echo t('Contact Info'); ?></h1>
          <div class="clear"></div>
        </div>
        <div class="block-fluid">
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'zip'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'zip'); ?> <span><?php echo $form->error($model,'zip'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'tele'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'tele'); ?> <span><?php echo $form->error($model,'tele'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'mobile'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'mobile'); ?> <span><?php echo $form->error($model,'mobile'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'mobile2'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'mobile2'); ?> <span><?php echo $form->error($model,'mobile2'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'fax'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'fax'); ?> <span><?php echo $form->error($model,'fax'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'skype'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'skype'); ?> <span><?php echo $form->error($model,'skype'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'email'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'email'); ?> <span><?php echo $form->error($model,'email'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form">
            <div class="span5"><?php echo $form->label($model,'email2'); ?></div>
            <div class="span7"> <?php echo $form->textField($model, 'email2'); ?> <span><?php echo $form->error($model,'email2'); ?></span> </div>
            <div class="clear"></div>
          </div>
          <div class="row-form" <?php if(user()->isVillaOwner){?> style=" display:none;"<?php } ?>>
            <div class="span5"><?php echo $form->label($model,'category'); ?></div>
            <div class="span7"> <?php echo $form->dropDownList($model,'category',  Category::GetAll(false), array()); ?> <span><?php echo $form->error($model,'category'); ?></span> </div>
            <div class="clear"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span9">
        <p>
          <button class="btn btn-large" type="submit"><?php echo t('Save'); ?></button>
        </p>
      </div>
    </div>
    <br class="clear" />
    <?php $this->endWidget(); ?>
  </div>
  <!-- form --> 
</div>
<!-- //Render Partial for Javascript Stuff --> 

