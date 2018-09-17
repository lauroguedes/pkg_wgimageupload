<?php
/**
 * @author          Andy Hickey <andy@netamity.com> customized by Lauro W. Guedes <leowgweb@gmail.com>
 * @link            http://www.leowgweb.com.br
 * @copyright       Copyright © 2018 leowgweb - All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');
JHtml::_('bootstrap.tooltip');

class JFormFieldWgimageupload extends JFormField
{
    protected $type = 'Wgimageupload';


    public function getInput()
    {
		//set session for authentication
		$session = JFactory::getSession();

		$settings = array(
		'maxwidth'=> $this->getAttribute('maxwidth') ,
		'minwidth'=> $this->getAttribute('minwidth') ,
		'maxheight'=> $this->getAttribute('maxheight') ,
		'minheight'=> $this->getAttribute('minheight') ,
		'destination'=> $this->getAttribute('destination') ,
		'img_filesize'=> $this->getAttribute('img_filesize') ,
		'acceptedformats'=> $this->getAttribute('acceptedformats') ,
        'filename_format'=> $this->getAttribute('filename_format'),
        'jtext' => [
            'INVALID_EXTENSION' => JText::_( 'INVALID_EXTENSION' ),
            'FILE_TO_LARGE_THAN_PHP_INI_ALLOWS' => JText::_( 'FILE_TO_LARGE_THAN_PHP_INI_ALLOWS' ),
            'FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS' =>  JText::_( 'FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS' ),
            'ERROR_PARTIAL_UPLOAD' => JText::_( 'ERROR_PARTIAL_UPLOAD' ),
            'ERROR_NO_FILE' => JText::_( 'ERROR_NO_FILE' ),
            'FILE_BIGGER_THAN_MB' => JText::sprintf( 'FILE_BIGGER_THAN_MB', ($this->getAttribute('img_filesize') / 1000) ),
            'IMAGE_TOO_WIDE' => JText::_( 'IMAGE_TOO_WIDE' ),
            'IMAGE_NOT_WIDE_ENOUGH' => JText::_( 'IMAGE_NOT_WIDE_ENOUGH' ),
            'IMAGE_TOO_TALL' => JText::_( 'IMAGE_TOO_TALL' ),
            'IMAGE_NOT_TALL_ENOUGH' => JText::_( 'IMAGE_NOT_TALL_ENOUGH' ),
            'INVALID_FILETYPE' => JText::_( 'INVALID_FILETYPE' ),
            'ERROR_MOVING_FILE' => JText::_( 'ERROR_MOVING_FILE' )
        ]
		);

        /*
         * Arrow the unique settings of the field.
         * The name attribute is concatenated so
         * that it may be possible to insert more extra
         * fields of type wguploadimage into a single page,
         * so separate sessions are created for each field loaded.
         * */
		$session->set('wgimageupload-' . $this->getAttribute('name'), $settings);

		$document = JFactory::getDocument();

        $document->addStyleSheet(Juri::root() . 'plugins/fields/wgimageupload/assets/wgimageupload.css');
		$document->addScriptDeclaration('var base = \''.JURI::root().'\'');
        $document->addStyleDeclaration('
            #jform_com_fields_'. str_replace('-', '_', $this->getAttribute('name')) .'-lbl{
                display: none;
            }
        ');

		JHtml::script(Juri::root() . 'plugins/fields/wgimageupload/assets/wgimageupload.js');

		if($this->value) { $prev = '<img src="'.$this->value.'"/><i id="remove-image' . $this->getAttribute('name') . '" title="Deletar Imagem" class="icon-remove"></i>'; } else { $prev = ''; }
        echo '<div class="wraper-upload-image"><div class="input-upload"><input type="file" class="inputfile inputfile-upload-edit-form" name="'. $this->getAttribute('name') .'" id="'. $this->getAttribute('name') .'"/><label for="'. $this->getAttribute('name') .'"><i class="icon-picture"></i> <strong>'. $this->getAttribute('label') . '</strong></label></div><div id="iu_result'. $this->getAttribute('name') .'" class="thumbnail">'.$prev.'</div><div id="iu_error'. $this->getAttribute('name') .'" class="upload-errors"></div></div>';
        //echo '<div id="iu_notice"></div>';
        echo '<input type="hidden" name="'.$this->name.'" id="setimgval'. $this->getAttribute('name') .'" value="'.$this->value.'" />';

		return;

    }
}