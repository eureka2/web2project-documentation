<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

echo "
mySettings = {
  previewParserPath:  '?m=documentation&a=preview&suppressHeaders=1&parser=BBCode', // path to your BBCode parser
  markupSet: [
    {name:'".$AppUI->_('Bold')."', key:'B', openWith:'[b]', closeWith:'[/b]'},
    {name:'".$AppUI->_('Italic')."', key:'I', openWith:'[i]', closeWith:'[/i]'},
    {name:'".$AppUI->_('Underline')."', key:'U', openWith:'[u]', closeWith:'[/u]'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Picture')."', key:'P', replaceWith:'[img][![Url]!][/img]'},
    {name:'".$AppUI->_('Link')."', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'".$AppUI->_('Your text to link here...')."'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Size')."', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
    dropMenu :[
      {name:'".$AppUI->_('Big')."', openWith:'[size=200]', closeWith:'[/size]' },
      {name:'".$AppUI->_('Normal')."', openWith:'[size=100]', closeWith:'[/size]' },
      {name:'".$AppUI->_('Small')."', openWith:'[size=50]', closeWith:'[/size]' }
    ]},
    {separator:'---------------' },
    {name:'".$AppUI->_('Bulleted list')."', openWith:'[list]\\n', closeWith:'\\n[/list]'},
    {name:'".$AppUI->_('Numeric list')."', openWith:'[list=[![".$AppUI->_('Starting number')."]!]]\\n', closeWith:'\\n[/list]'}, 
    {name:'".$AppUI->_('List item')."', openWith:'[*] '},
    {separator:'---------------' },
    {name:'".$AppUI->_('Quotes')."', openWith:'[quote]', closeWith:'[/quote]'},
    {name:'".$AppUI->_('Code')."', openWith:'[code]', closeWith:'[/code]'}, 
    {separator:'---------------' },
    {name:'".$AppUI->_('Clean')."', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/\\[(.*?)\\]/g, \"\") } },
    {name:'".$AppUI->_('Preview')."', className:'preview', call:'preview' }
  ]
};
";