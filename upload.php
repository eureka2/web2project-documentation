<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

$error = null;
switch ($_FILES['wpUploadFile']['error']) 
{
    case UPLOAD_ERR_OK :
        $filename = basename($_FILES['wpUploadFile']['name']);
        list($width, $height, $type, $attr) = getimagesize($_FILES['wpUploadFile']['tmp_name']);
        $extensions = array('.png', '.gif', '.jpg', '.jpeg', '.ogg');
        $extension = image_type_to_extension ($type); 
        $destfile = w2PgetParam($_POST, 'wpDestFile', "");
        if ($destfile) {
            $filename = $destfile.$extension;
        }
        if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $filename) ) { // Security check...
            $error = 'Filename contains forbidden characters';
        } elseif(!in_array(strtolower($extension), $extensions)) { 
            $error = 'Invalid extension';
        }
        break;
    case UPLOAD_ERR_INI_SIZE :
        $error = 'UPLOAD_ERR_INI_SIZE';
        break;
    case UPLOAD_ERR_FORM_SIZE :
        $error = 'UPLOAD_ERR_FORM_SIZE';
        break;
    case UPLOAD_ERR_PARTIAL :
        $error = 'UPLOAD_ERR_PARTIAL';
        break;
    case UPLOAD_ERR_NO_FILE :
        $error = 'UPLOAD_ERR_NO_FILE';
        break;
    case UPLOAD_ERR_NO_TMP_DIR :
        $error = 'UPLOAD_ERR_NO_TMP_DIR';
        break;
    case UPLOAD_ERR_CANT_WRITE :
        $error = 'UPLOAD_ERR_CANT_WRITE';
        break;
    case UPLOAD_ERR_EXTENSION :
        $error = 'UPLOAD_ERR_EXTENSION';
        break;
    default :
        $error = 'UPLOAD_ERR_UNKNOWN';
}
if(is_null($error)) { 
    $filename = strtr(
        $filename, 
        'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
        'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy'
    );
    $filename = preg_replace('/([^.a-z0-9]+)/i', '-', $filename);
    $realfile = W2P_BASE_DIR . '/modules/documentation/images/upload/'.$filename;
    if(move_uploaded_file($_FILES['wpUploadFile']['tmp_name'], $realfile)) {
        $description = w2PgetParam($_POST, 'wpUploadDescription', "");
        $image = new CDocumentation();
        $image->wikipage_namespace = "Image";
        $image->wikipage_name = $filename;
        $image->wikipage_title = $filename;
        $image->wikipage_content = $description;
        if (($msg = $image->store($AppUI) !== true)) {
            unlink($realfile);
            $data = array('error' => $msg);
        } else {
            $size = filesize($realfile);
            list($imgWidth, $imgHeight, $imgType, $imgAttr) = getimagesize($realfile);
            $data = array(
                'filename' => $filename,
                'summary' => $description,
                'size' => $size,
                'width' => $imgWidth,
                'height' => $imgHeight,
                'type' => image_type_to_mime_type($imgType)
            );
        }
    } else {
        $data = array('error' => "Upload fail");
    }
} else {
    $data = array('error' => $error);
}

header('Content-type: text/html');
echo json_encode($data);
