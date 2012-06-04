<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

$parser = defVal(w2PgetParam($_REQUEST, 'parser', null), '');

require ("engines/".$parser."/parser.php");

$wiki = new CWiki('', true);
$parser = new $parser($wiki);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Preview</title>
<meta name="Description" content="web2Project Default Style" />
<meta name="Version" content="<?php echo $AppUI->getVersion(); ?>" />
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8'; ?>" />
<link rel="stylesheet" type="text/css" href="style/common.css" media="all" charset="utf-8"/>
<link rel="stylesheet" type="text/css" href="./style/<?php echo $uistyle; ?>/main.css" media="all" charset="utf-8"/>
<style type="text/css" media="all">@import "./style/<?php echo $uistyle; ?>/main.css"; body{background-color:white; font-size: 9pt;}</style>
<link rel="stylesheet" type="text/css" href="modules/documentation/css/documentation.css" media="all" charset="utf-8"/>
<script type="text/javascript" src="lib/jquery/jquery.js"></script>
</head>
<body>
<div id="documentation">
<?php echo $parser->parse($_POST['data'], 'preview', time()); ?>
</div>
</body>
</html>
