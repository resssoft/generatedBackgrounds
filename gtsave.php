<?php
if (!isset($_REQUEST['imagedata'])) {exit(':)');}
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function base64_to_jpeg($base64_string, $output_file) {
    $ifp = fopen($output_file, "wb"); 

    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp); 

    return $output_file; 
}
$filepath = '/gtimages/';
$filename = generateRandomString(20) . '.png';
$filenamewithpath = $filepath . $filename;
//$image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAABDUlEQVQoU9XRQUrDQBQG4D+X8ATewMN4A49QRBHBjRQVwZUiRcGNF3AnFMWNGzdKorYjMXYUi0natMlMx848J7rrZNEsneWb9z14//MOz5skJwJCFiAymH1CSLQvr3Bzce/N/nm7rXXi/QifXxxKTRys1DdeAo6Ha+7i7aMGdV59hL1nFDJ3sNGEuCfAblMXN49XqWsxe3tCIcYO1lODhMtqvNNaszgAix4xLrJ6eO9kg5jF5YAsH9TD+6ebxKI/PBgl9fDB2RZ1Qt9iH2kWVwaW2MC6VYGtNJapH7/bU33YnYcutqfPU4Xwbuimvbi0QFpraDMFGXJwWSjPxYORiyu75yw60+Z0v23/FP8AclG6EFLL6b0AAAAASUVORK5CYII=';
$image = $_REQUEST['imagedata'];
base64_to_jpeg($image, $_SERVER['DOCUMENT_ROOT'] . $filenamewithpath);

if (isset($_REQUEST['ajax'])) {
echo json_encode(array('filename' => $filename, 'filenamewithpath' => $filenamewithpath));
} else { ?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      body {
        margin: 0px;
        padding: 0px;
      }
    </style>
  </head>
  <body>
    <img src="<?php echo $filepath; ?>" />
  </body>
</html>
<?php } ?>
