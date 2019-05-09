<?

function print_to_file($var, $f="log.txt", $namesection=''){
    $fp = fopen($_SERVER['DOCUMENT_ROOT']."/logs/" . $f, "a+");
    fwrite($fp, "-------" .  date('d-m-Y H:m:s') . "------- //".$namesection."\n");
	fwrite($fp, print_r($var, true)."\n");
    fclose($fp);
}

print_to_file($_SERVER['HTTP_REFERER'],'users.log', 'HTTP_REFERER');
print_to_file($_SERVER['REMOTE_ADDR'],'users.log', 'REMOTE_ADDR');
print_to_file($_SERVER['HTTP_X_REAL_IP'],'users.log', 'HTTP_X_REAL_IP');
print_to_file($_SERVER['HTTP_X_FORWARDED_FOR'],'users.log', 'HTTP_X_FORWARDED_FOR');
print_to_file($_SERVER['HTTP_USER_AGENT'],'users.log', 'HTTP_USER_AGENT');
print_to_file($_SERVER['QUERY_STRING'],'users.log', 'QUERY_STRING');
print_to_file('#############################################','users.log');



?>
<!DOCTYPE HTML>
<html>
  <head>
    <style>
    <?php if (!isset($_REQUEST['showscroll'])) { ?>
      body {
        margin: 0px;
        padding: 0px;
        overflow: hidden;
      }
      <?php } ?>
* {
 padding: 0;
 margin: 0;
}


.tablediv {
 position: relative;
 display: inline-block;
 overflow: hidden;
}

#myCanvas {
display: none;
}

.dark {
position: absolute;
z-index: 100;
background: radial-gradient(circle at 50% 50%, rgba(255, 255, 3, 0), #000000);
width: 100%;
height: 100%;
background-size: cover;
}
    </style>

<script type="text/javascript" src="/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="/js/jBox.min.js"></script>
  </head>
  <body>
  <div id="parentdiv" class="tablediv">
  <!--<div class="dark"></div>-->
    <canvas id="myCanvas" width="1550" height="850"></canvas>
    <img id="renderedImage" alt="Right click to save me!">
    <script>
<?php if (isset($_REQUEST['clist'])) {
$clist_string = htmlspecialchars($_REQUEST['clist']);
//if (strlen($clist_string ) > 50 ) exit('_lol_');
$clist = explode("_", $clist_string);
//$clistcount = count($clist)-1;
$js_arr_vals = '';
foreach ($clist as $clist_val) {
$js_arr_vals .= "'" . $clist_val . "',";
}
//$js_arr_vals .= "'F00'";
echo 'var clist = ['.$js_arr_vals.'];' . "\n";
echo 'var clist_count = clist.length;' . "\n";
} 
?>
function actualHeight() {
  var myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientHeight ) ) {
    //IE 4 compatible
    myHeight = document.body.clientHeight;
  }
  return myHeight;
}

function actualWidth() {
  var myWidth = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
  } else if( document.documentElement && ( document.documentElement.clientWidth) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
  } else if( document.body && ( document.body.clientWidth) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
  }
  return myWidth;
}

      function getRandomInt(min, max) {
          return Math.floor(Math.random() * (max - min + 1)) + min;
      }
function getImageFile (changesrcflag) {
  jQuery.ajax({
    url: '/gtsave.php',
    type: "POST",
    data: {
        imagedata: jQuery('#renderedImage').attr('src'),
        ajax: 1
    },
  }).done(function(data) {
    obj = JSON && JSON.parse(data) || jQuery.parseJSON(data);
    jQuery('#renderedImage').attr('src', obj.filenamewithpath);
  });
}
jQuery(document).bind('keydown', 'ctrl+c', getImageFile);

function drawSoftLine(x1, y1, x2, y2, lineWidth, r, g, b, a) {
   var lx = x2 - x1;
   var ly = y2 - y1;
   var lineLength = Math.sqrt(lx*lx + ly*ly);
   var wy = lx / lineLength * lineWidth;
   var wx = ly / lineLength * lineWidth;
   var gradient = ctx.createLinearGradient(x1-wx/2, y1+wy/2, x1+wx/2, y1-wy/2);
      // The gradient must be defined accross the line, 90° turned compared
      // to the line direction.
   gradient.addColorStop(0,    "rgba("+r+","+g+","+b+",0)");
   gradient.addColorStop(0.43, "rgba("+r+","+g+","+b+","+a+")");
   gradient.addColorStop(0.57, "rgba("+r+","+g+","+b+","+a+")");
   gradient.addColorStop(1,    "rgba("+r+","+g+","+b+",0)");
   ctx.save();
   ctx.beginPath();
   ctx.lineWidth = lineWidth;
   ctx.strokeStyle = gradient;
   ctx.moveTo(x1, y1);
   ctx.lineTo(x2, y2);
   ctx.stroke();
   ctx.restore();
}

document.addEventListener("DOMContentLoaded", function(){
    var canvasW = <?php if (isset($_REQUEST['width'])) { echo intval($_REQUEST['width']);} else { ?>actualWidth()-5 <?php } ?>;
    var canvasH = <?php if (isset($_REQUEST['height'])) { echo intval($_REQUEST['height']);} else { ?>actualHeight()-5 <?php } ?>;

    document.getElementById('parentdiv').style.width = canvasW + 'px';
    document.getElementById('parentdiv').style.height = canvasH + 'px';

    var canvas = document.getElementById('myCanvas');
        canvas.width = canvasW;
        canvas.height = canvasH;

//setInterval(function() {
      var imageObj = new Image(); var color_number, dx, dy;
      var imagebase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QoCBhcvKBKwBgAAABl0RVh0Q29tbWVudABDcmVhdGVkIHdpdGggR0lNUFeBDhcAAAClSURBVBjTlc47CsJQEIXhf7yBCCJY3CZuxMadxNpV2LqJLCibCBFikzaNcEMexyZCCEZw4MAUH2fGJN2A15SRxdR17dI09RHQAA/gCYQlLMsyLorijKSrpJOkvSRbJkmSnZldImADOMAAmdmyVMyQm/bV+QvOsf2Cnz/NzPTFjICiLMu2VVUd8zwfnHPNMAyaNQuIgYN57+9t2/YhhL7runHl8vYNQ/dMRQ59ZcIAAAAASUVORK5CYII=';
      var canvas = document.getElementById('myCanvas');
      var context = canvas.getContext('2d');
      var image_height = 10, image_width = 10;
      var rows = Math.floor(canvasW / image_height), columns = Math.floor(canvasW / image_width);
      //var rows = Math.floor(document.width / image_height), columns = Math.floor(document.width / image_width);
console.log(document.width);console.log(image_height );
console.log(rows );console.log(columns );
//rows = 5;
//columns = 5;
      // make ajax call to get image data url
        // Makes sure the document is ready to parse.
        if(1) {
          // Makes sure it's found the file.
          if(1) {
            imageObj.src = imagebase64;
            for (var i = 0; i < rows; i++) {
                for  (var y = 0; y < columns; y++) {
<?php if (isset($_REQUEST['colors'])) {
$color_string = htmlspecialchars($_REQUEST['colors']);
//if (strlen($color_string) > 50 ) exit('_lol_');
$colors = explode("_", $color_string);
echo "color_number = '#' + getRandomInt(".$colors[0].", ".$colors[1].").toString(16) + '' + getRandomInt(".$colors[2].", ".$colors[3].").toString(16) + '' + getRandomInt(".$colors[4].", ".$colors[5].").toString(16);";
} else {
?>
                    color_number = '#' + getRandomInt(31, 77).toString(16) + '' + getRandomInt(53, 145).toString(16) + '' + getRandomInt(16, 29).toString(16); // green
<?php } ?>

<?php if (isset($_REQUEST['clist'])) { ?>
var random = getRandomInt(0, clist_count*1-1);
color_number = '#' + clist[random];
<?php } ?>
//console.log(color_number);
                    dx = y*image_width
                    dy = i*image_height;
                    context.fillStyle=color_number;
                    context.fillRect(dx,dy,image_width,image_height);
                    // load image from data url
                    context.drawImage(imageObj, dx, dy);
                    //imageObj.onload = function() {
                    //    context.drawImage(imageObj, dx, dy);
                    /}
                }
            }
          }
        };
<?php if (!isset($_REQUEST['clear'])) { ?>

var rw1 = Math.floor(canvasW/2);
var rh1 = Math.floor(canvasH/2);
var r1 = Math.floor(canvasH*<?php if (isset($_REQUEST['radial-radius-1'])) { echo floatval($_REQUEST['radial-radius-1']);} else { ?>0.09 <?php } ?>);
var r2 = Math.floor(canvasW*<?php if (isset($_REQUEST['radial-radius-2'])) { echo floatval($_REQUEST['radial-radius-2']);} else { ?>0.55 <?php } ?>);


var grd=context.createRadialGradient(rw1,rh1,r1,rw1+1,rh1+1,r2);
grd.addColorStop(0,'rgba(0,0,0,0.0)');
grd.addColorStop(1,"#000000");

// Fill with gradient
context.fillStyle=grd;
context.fillRect(0,0,canvasW,canvasH);
<?php } ?>
var dataURL = canvas.toDataURL();
document.getElementById('renderedImage').src = dataURL;
//}, 1000);
}, true);

    </script>
  </div>
  </body>
</html>
