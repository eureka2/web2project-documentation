<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

echo "
mySettings = {
	previewParserPath: '?m=documentation&a=preview&suppressHeaders=1&parser=Markdown', // path to your Wiki parser
  onShiftEnter:      {keepDefault:false, openWith:'\\n\\n'},
  markupSet: [
    {name:'".$AppUI->_('First Level Heading')."', key:'1', placeHolder:'".$AppUI->_('Your title here...')."', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '=') } },
    {name:'".$AppUI->_('Second Level Heading')."', key:'2', placeHolder:'".$AppUI->_('Your title here...')."', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '-') } },
    {name:'".$AppUI->_('Heading 3')."', key:'3', openWith:'### ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 4')."', key:'4', openWith:'#### ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 5')."', key:'5', openWith:'##### ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 6')."', key:'6', openWith:'###### ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {separator:'---------------' },    
    {name:'".$AppUI->_('Bold')."', key:'B', openWith:'**', closeWith:'**'},
    {name:'".$AppUI->_('Italic')."', key:'I', openWith:'_', closeWith:'_'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Bulleted List')."', openWith:'- ' },
    {name:'".$AppUI->_('Numeric List')."', openWith:function(markItUp) {
      return markItUp.line+'. ';
    }},
    {separator:'---------------' },
    {name:'".$AppUI->_('Picture')."', key:'P', replaceWith:'![[![".$AppUI->_('Alternative text')."]!]]([![Url:!:http://]!] \"[![".$AppUI->_('Title')."]!]\")'},
    {name:'".$AppUI->_('Link')."', key:'L', openWith:'[', closeWith:']([![Url:!:http://]!] \"[![".$AppUI->_('Title')."]!]\")', placeHolder:'".$AppUI->_('Your text to link here...')."' },
    {separator:'---------------'},  
    {name:'".$AppUI->_('Quotes')."', openWith:'> '},
    {name:'".$AppUI->_('Code Block / Code')."', openWith:'(!(\\t|!|`)!)', closeWith:'(!(`)!)'},
    {separator:'---------------'},
    {name:'".$AppUI->_('Preview')."', call:'preview', className:\"preview\"}
  ]
};

// mIu nameSpace to avoid conflict.
miu = {
  markdownTitle: function(markItUp, char) {
    heading = '';
    n = jQuery.trim(markItUp.selection||markItUp.placeHolder).length;
    for(i = 0; i < n; i++) {
      heading += char;
    }
    return '\\n'+heading;
  }
};
";
