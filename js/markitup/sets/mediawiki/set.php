<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

echo "
mySettings = {
    previewParserPath:    '?m=documentation&a=preview&suppressHeaders=1&parser=MediaWiki', // path to your Wiki parser
  previewAutoRefresh: true,
    onShiftEnter:        {keepDefault:false, replaceWith:'\\n\\n'},
    markupSet: [
        {name:'".$AppUI->_('Heading 1')."', key:'1', openWith:'== ', closeWith:' ==', placeHolder:'".$AppUI->_('Your title here...')."' },
        {name:'".$AppUI->_('Heading 2')."', key:'2', openWith:'=== ', closeWith:' ===', placeHolder:'".$AppUI->_('Your title here...')."' },
        {name:'".$AppUI->_('Heading 3')."', key:'3', openWith:'==== ', closeWith:' ====', placeHolder:'".$AppUI->_('Your title here...')."' },
        {name:'".$AppUI->_('Heading 4')."', key:'4', openWith:'===== ', closeWith:' =====', placeHolder:'".$AppUI->_('Your title here...')."' },
        {name:'".$AppUI->_('Heading 5')."', key:'5', openWith:'====== ', closeWith:' ======', placeHolder:'".$AppUI->_('Your title here...')."' },
        {separator:'---------------' },        
        {name:'".$AppUI->_('Bold')."', key:'B', openWith:\"'''\", closeWith:\"'''\"}, 
        {name:'".$AppUI->_('Italic')."', key:'I', openWith:\"''\", closeWith:\"''\"}, 
        {name:'".$AppUI->_('Stroke through')."', key:'S', openWith:'<s>', closeWith:'</s>'}, 
        {separator:'---------------' },
        {name:'".$AppUI->_('Bulleted list')."', openWith:'(!(* |!|*)!)'}, 
        {name:'".$AppUI->_('Numeric list')."', openWith:'(!(# |!|#)!)'}, 
        {separator:'---------------' },
        {name:'".$AppUI->_('Picture')."', key:'P', replaceWith: function (h) { MarkupHelper.askImage(h); return false; } },
        {name:'".$AppUI->_('Link')."', key:'L', replaceWith: function (h) { MarkupHelper.askLink(h); return false; } },
        {name:'".$AppUI->_('Url')."', openWith:'[[![Url:!:http://]!] ', closeWith:']', placeHolder:'".$AppUI->_('Your text to link here...')."' },
        {separator:'---------------' },
        {name:'".$AppUI->_('Quotes')."', openWith:'(!(> |!|>)!)', placeHolder:''},
        {name:'".$AppUI->_('Code')."', replaceWith: function (h) { MarkupHelper.askCode(h); return false; } },
        {separator:'---------------' },
        {name:'".$AppUI->_('Preview')."', call:'preview', className:'preview'}
    ]
};
";

