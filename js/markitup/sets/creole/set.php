<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

echo "
var mySettings = {
	previewParserPath:	'?m=documentation&a=preview&suppressHeaders=1&parser=Creole', // path to your Wiki parser
  onShiftEnter: {keepDefault: false, replaceWith: '\\\\\\\\'},
  onCtrlEnter: {keepDefault: false, replaceWith: '\\n\\n'},
  markupSet: [
    {name:'".$AppUI->_('Heading 1')."', key:'1', openWith:'= ', closeWith:' =', className:'h1', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 2')."', key:'2', openWith:'== ', closeWith:' ==', className:'h2', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 3')."', key:'3', openWith:'=== ', closeWith:' ===', className:'h3', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 4')."', key:'4', openWith:'==== ', closeWith:' ====', className:'h4', placeHolder:'".$AppUI->_('Your title here...')."' },
    {separator:'---------------' },
    {name:'".$AppUI->_('Bold')."', key:'B', openWith:'**', closeWith:'**', className:'bold', placeHolder:'".$AppUI->_('Your text here...')."'},
    {name:'".$AppUI->_('Italic')."', key:'I', openWith:'//', closeWith:'//', className:'italic', placeHolder:'".$AppUI->_('Your text here...')."'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Bulleted list')."', openWith:'* ', className:'list-bullet'},
    {name:'".$AppUI->_('Numeric list')."', openWith:'# ', className:'list-numeric'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Picture')."', key:'P', replaceWith:'{{[![Url:!:http://]!]|[![".$AppUI->_('Alternative text')."]!]}}', className:'image'},
    {name:'".$AppUI->_('Link')."', key:'L', replaceWith:'[[[![Url:!:http://]!]|[![".$AppUI->_('Title')."]!]]]', className:'link'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Code block')."', openWith:'{{{', closeWith:'}}}', className:'code'}
  ]
};
";

