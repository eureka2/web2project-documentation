<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

echo "
mySettings = {
  previewParserPath:  '?m=documentation&a=preview&suppressHeaders=1&parser=Textile', // path to your Textile parser
  onShiftEnter:    {keepDefault:false, replaceWith:'\\n\\n'},
  markupSet: [
    {name:'".$AppUI->_('Heading 1')."', key:'1', openWith:'h1(!(([![Class]!]))!). ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 2')."', key:'2', openWith:'h2(!(([![Class]!]))!). ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 3')."', key:'3', openWith:'h3(!(([![Class]!]))!). ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 4')."', key:'4', openWith:'h4(!(([![Class]!]))!). ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 5')."', key:'5', openWith:'h5(!(([![Class]!]))!). ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Heading 6')."', key:'6', openWith:'h6(!(([![Class]!]))!). ', placeHolder:'".$AppUI->_('Your title here...')."' },
    {name:'".$AppUI->_('Paragraph')."', key:'P', openWith:'p(!(([![Class]!]))!). '},
    {separator:'---------------' },
    {name:'".$AppUI->_('Bold')."', key:'B', closeWith:'*', openWith:'*'},
    {name:'".$AppUI->_('Italic')."', key:'I', closeWith:'_', openWith:'_'},
    {name:'".$AppUI->_('Stroke through')."', key:'S', closeWith:'-', openWith:'-'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Bulleted list')."', openWith:'(!(* |!|*)!)'},
    {name:'".$AppUI->_('Numeric list')."', openWith:'(!(# |!|#)!)'}, 
    {separator:'---------------' },
    {name:'".$AppUI->_('Picture')."', replaceWith:'![![".$AppUI->_('Source').":!:http://]!]([![".$AppUI->_('Alternative text')."]!])!'}, 
    {name:'".$AppUI->_('Link')."', openWith:'\"', closeWith:'([![".$AppUI->_('Title')."]!])\":[![".$AppUI->_('Link').":!:http://]!]', placeHolder:'".$AppUI->_('Your text to link here...')."' },
    {separator:'---------------' },
    {name:'".$AppUI->_('Quotes')."', openWith:'bq(!(([![Class]!]))!). '},
    {name:'".$AppUI->_('Code')."', openWith:'@', closeWith:'@'},
    {separator:'---------------' },
    {name:'".$AppUI->_('Preview')."', call:'preview', className:'preview'}
  ]
};
";
