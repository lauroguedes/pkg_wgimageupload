<?php
/**
 * @author          Andy Hickey <andy@netamity.com> customized by Lauro W. Guedes <leowgweb@gmail.com>
 * @link            http://www.leowgweb.com.br
 * @copyright       Copyright © 2018 leowgweb - All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */
 defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.plugin.plugin');

class plgAjaxWgimageupload extends JPlugin
{

	function onAjaxWgimageupload()
	{
	    //sleep(5);
	    //get user logado
        $user    = JFactory::getUser();

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $jinput = JFactory::getApplication()->input;
        $imgfile = $jinput->files->get('pictureFile');
        $fileError = $imgfile['error'];

        //get unique name of field
        $ref = $jinput->get('referenceField');

		//get setting or leave
		$session = JFactory::getSession();
		$settings = $session->get('wgimageupload-' . $ref);
		if(!$settings)
		{
			jexit();
		}
		
        if ($fileError > 0)
        {
            switch ($fileError)
            {
                case 1:
                    return( json_encode(array('error'=>  $settings['jtext']['FILE_TO_LARGE_THAN_PHP_INI_ALLOWS'])));

                case 2:
                    return( json_encode(array('error'=>  $settings['jtext']['FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS'])));

                case 3:
                    return( json_encode(array('error'=>  $settings['jtext']['ERROR_PARTIAL_UPLOAD'])));

                case 4:
                    return( json_encode(array('error'=>  $settings['jtext']['ERROR_NO_FILE'])));

            }
        }

//check for filesize
        $fileSize = $imgfile['size'];
		$mfs = $settings['img_filesize'] * 1000;
		$sts = $settings['img_filesize'] / 1000;
        if($fileSize > $mfs)
        {
        return( json_encode(array('error'=> $settings['jtext']['FILE_BIGGER_THAN_MB'])));
        }

//check the file extension is ok
        $fileName = $imgfile['name'];
        $uploadedFileNameParts = explode('.',$fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = explode(',', $settings['acceptedformats']);

//assume the extension is false until we know its ok
        $extOk = false;

//go through every ok extension, if the ok extension matches the file extension (case insensitive)
//then the file extension is ok
        foreach($validFileExts as $key => $value)
        {
            if( preg_match("/$value/i", $uploadedFileExtension ) )
            {
                $extOk = true;
            }
        }

        if ($extOk == false)
        {
			return( json_encode(array('error'=> $settings['jtext']['INVALID_EXTENSION'])));
        }

//the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $imgfile['tmp_name'];

//for security purposes, we will also do a getimagesize on the temp file (before we have moved it
//to the folder) to check the MIME type of the file, and whether it has a width and height
        $imageinfo = getimagesize($fileTemp);
		list($w, $h) = getimagesize($fileTemp);
		
		if($w > $settings['maxwidth'])
		{
			return( json_encode(array('error'=> $settings['jtext']['IMAGE_TOO_WIDE'])) );
		}
		if($w < $settings['minwidth'])
		{
			return( json_encode(array('error'=> $settings['jtext']['IMAGE_NOT_WIDE_ENOUGH'])) );
		}
		if($h > $settings['maxheight'])
		{
			return( json_encode(array('error'=> $settings['jtext']['IMAGE_TOO_TALL'])) );
		}
		if($h < $settings['minwidth'])
		{
			return( json_encode(array('error'=> $settings['jtext']['IMAGE_NOT_TALL_ENOUGH'])) );
		}

//all possible options for mimetype are allowed, and images are filtered based on file extension
        $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif,image/bmp';
        $validFileTypes = explode(",", $okMIMETypes);

//if the temp file does not have a width or a height, or it has a non ok MIME, return
        if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) )
        {
            return( json_encode(array('error'=> $settings['jtext']['INVALID_FILETYPE'])));
        }

//lose any special characters in the filename, but fix issue with filename dots being lost
		$fileName2 = explode('.',$fileName);
        $fileName = preg_replace("/[^A-Za-z0-9]/i", "-", $fileName2[0]);
//now set preferred filename format
		 if($settings['filename_format'] == 0)
		{
			$fileName = $fileName . "_" . time();
		}
		else if($settings['filename_format'] == 1)
		{
			$fileName = $fileName . "_" . rand(100000,999999);
		}
		else if($settings['filename_format'] == 2)
		{
			$fileName = $fileName . "_" . substr(md5(microtime()),rand(0,26),12);
		} 
		
		
		$fileName = $fileName . "." . $fileName2[1];

//always use constants when making file paths, to avoid the possibilty of remote file inclusion

		$path = $settings['destination'] . '/' . $user->id;
		if(!$path)
		{
			$path = 'images';
		}
		else
		{
			$path = 'images' . DIRECTORY_SEPARATOR . $path;
		}
		
        $uploadPath = JPATH_SITE. DIRECTORY_SEPARATOR .$path. DIRECTORY_SEPARATOR .$fileName;
		$relPath = DIRECTORY_SEPARATOR . $path. DIRECTORY_SEPARATOR .$fileName;

        if(!JFile::upload($fileTemp, $uploadPath))
        {
            return( json_encode(array('error'=> $settings['jtext']['ERROR_MOVING_FILE'] )));
        }
        else
        {
            $rtn = array('error'=> false , 'filename' => $fileName , 'relpath' => $relPath);
			return json_encode($rtn, JSON_UNESCAPED_SLASHES);
        }

	}
}
