<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

echo "
mySettings = {
  onShiftEnter: {keepDefault:false, replaceWith:'<br />\\n'},
  onCtrlEnter:  {keepDefault:false, openWith:'\\n<p>', closeWith:'</p>'},
  onTab:        {keepDefault:false, replaceWith:'    '},
  markupSet:  [   
    {name:'".$AppUI->_('Bold')."', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
    {name:'".$AppUI->_('Italic')."', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
    {name:'".$AppUI->_('Stroke through')."', key:'S', openWith:'<del>', closeWith:'</del>' },
    {separator:'---------------' },
    {name:'".$AppUI->_('Picture')."', key:'P', replaceWith:'<img src=\"[![Source:!:http://]!]\" alt=\"[![".$AppUI->_('Alternative text')."]!]\" />' },
    {name:'".$AppUI->_('Link')."', key:'L', openWith:'<a href=\"[![Link:!:http://]!]\"(!( title=\"[![Title]!]\")!)>', closeWith:'</a>', placeHolder:'".$AppUI->_('Your text to link...')."' },
    {separator:'---------------' },
    {name:'".$AppUI->_('Clean')."', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, \"\") } },    
    {name:'".$AppUI->_('Preview')."', className:'preview',  call:'preview'}
  ]
};
";