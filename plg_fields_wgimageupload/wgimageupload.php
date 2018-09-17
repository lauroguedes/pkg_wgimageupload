<?php
/**
 * @author          Andy Hickey <andy@netamity.com> customized by Lauro W. Guedes <leowgweb@gmail.com>
 * @link            http://www.leowgweb.com.br
 * @copyright       Copyright © 2018 leowgweb - All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);


class PlgFieldsWgimageupload extends FieldsPlugin
{
    public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);
        

        if (!$fieldNode)
        {
            return $fieldNode;
        }

        $form->addFieldPath(JPATH_PLUGINS . '/fields/wgimageupload/fields');
        $fieldNode->setAttribute('type', 'wgimageupload');

        return $fieldNode;
    }
}
