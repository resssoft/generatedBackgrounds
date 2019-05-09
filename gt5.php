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
<script type="text/javascript" src="/js/StackBlur.js"></script>
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

function drawSoftLine(context, x1, y1, x2, y2, lineWidth, r, g, b, a) {
   var lx = x2 - x1;
   var ly = y2 - y1;
   var lineLength = Math.sqrt(lx*lx + ly*ly);
   var wy = lx / lineLength * lineWidth;
   var wx = ly / lineLength * lineWidth;
   var gradient = context.createLinearGradient(x1-wx/2, y1+wy/2, x1+wx/2, y1-wy/2);
      // The gradient must be defined accross the line, 90° turned compared
      // to the line direction.
   gradient.addColorStop(0,    "rgba("+r+","+g+","+b+",0)");
   gradient.addColorStop(0.43, "rgba("+r+","+g+","+b+","+a+")");
   gradient.addColorStop(0.57, "rgba("+r+","+g+","+b+","+a+")");
   gradient.addColorStop(1,    "rgba("+r+","+g+","+b+",0)");
   context.save();
   context.beginPath();
   context.lineWidth = lineWidth;
   context.strokeStyle = gradient;
   context.moveTo(x1, y1);
   context.lineTo(x2, y2);
   context.stroke();
   context.restore();
}

function drawSoftCircle(context, centerX, centerY, radius, lineWidth, r, g, b, a) {
   context.beginPath();
   context.arc(centerX, centerY, radius, 0, 2 * Math.PI, false);
   //context.fillStyle = 'green';
   //context.fill();
   context.lineWidth = lineWidth;
   context.strokeStyle = 'rgba('+r+','+g+','+b+','+a+')';
   context.fillStyle = 'rgba('+r+','+g+','+b+','+a+')';
   context.stroke();
}

function drawStar(context, cx,cy,spikes,outerRadius,innerRadius, lineWidth, fillit, r, g, b, a){
      var rot=Math.PI/2*3;
      var x=cx;
      var y=cy;
      var step=Math.PI/spikes;

      //context.strokeSyle="#000";
      context.strokeStyle = 'rgba('+r+','+g+','+b+','+a+')';
      context.lineWidth = lineWidth;
      context.beginPath();
      context.moveTo(cx,cy-outerRadius)
      for(i=0;i<spikes;i++){
        x=cx+Math.cos(rot)*outerRadius;
        y=cy+Math.sin(rot)*outerRadius;
        context.lineTo(x,y)
        rot+=step

        x=cx+Math.cos(rot)*innerRadius;
        y=cy+Math.sin(rot)*innerRadius;
        context.lineTo(x,y)
        rot+=step
      }
      context.lineTo(cx,cy-outerRadius);
      if (fillit = 1) {
      context.fill();
      }
      context.stroke();
      context.closePath();
    }

    

document.addEventListener("DOMContentLoaded", function(){
    var canvasW = <?php if (isset($_REQUEST['width'])) { echo intval($_REQUEST['width']);} else { ?>actualWidth()-5 <?php } ?>;
    var canvasH = <?php if (isset($_REQUEST['height'])) { echo intval($_REQUEST['height']);} else { ?>actualHeight()-5 <?php } ?>;

    document.getElementById('parentdiv').style.width = canvasW + 'px';
    document.getElementById('parentdiv').style.height = canvasH + 'px';

    var canvas = document.getElementById('myCanvas');
    canvas.width = canvasW;
    canvas.height = canvasH;
    var centerX = canvas.width / 2;
    var centerY = canvas.height / 2;
    var color_number, dx, dy;
    var context = canvas.getContext('2d');


<?php if (!isset($_REQUEST['clear'])) { ?>

var rw1 = Math.floor(canvasW/2);
var rh1 = Math.floor(canvasH/2);
var r1 = Math.floor(canvasH*<?php if (isset($_REQUEST['radial-radius-1'])) { echo floatval($_REQUEST['radial-radius-1']);} else { ?>0.09 <?php } ?>);
var r2 = Math.floor(canvasW*<?php if (isset($_REQUEST['radial-radius-2'])) { echo floatval($_REQUEST['radial-radius-2']);} else { ?>0.55 <?php } ?>);


var grd=context.createRadialGradient(rw1,rh1,r1,rw1+1,rh1+1,r2);
grd.addColorStop(0,'rgba(238,238,238,0.9)');
grd.addColorStop(1,'rgba(0,0,0,0.0)');

// Fill with gradient
context.fillStyle=grd;
context.fillRect(0,0,canvasW,canvasH);
<?php } ?>

    context.save();
    context.rect(0,0,canvasW,canvasH);
    context.fillStyle = 'rgba(245,235,88,1.0)';
    context.strokeStyle  = 'rgba(245,235,88,1.0)';
    context.fill();

    var rows = getRandomInt(0, 50);
    var x=0,y=0;

    for (var i = 0; i < rows; i++) {
    r = getRandomInt(20, 100);
    x = getRandomInt(r+10, 1400-r);
    y = getRandomInt(r+10, 700-r);
    x1 = getRandomInt(0, 1400);
    y1 = getRandomInt(0, 700);
    //drawSoftLine(context, x, y, getRandomInt(0, 1400), getRandomInt(0, 700), 9, 0, 0, 0, 1);
    drawSoftCircle(context, x, y, r, 2, 28, 29, 29, 0.9);
    drawSoftCircle(context, x, y, r-15, 15, 28, 29, 29, 1.0);

    x = getRandomInt(r+10, 1400-r);
    y = getRandomInt(r+10, 700-r);
    drawStar(context,x,y,5,10,15, 2, 0, 28, 29, 29, 0.9);
stackBlurCanvasRGBA( 'myCanvas', 0, 0, canvasW, canvasH, 2, 2 );
    /*
    ctx.moveTo(x, y);
    x = getRandomInt(0, 1400);
    y = getRandomInt(0, 700);
    ctx.lineTo(x, y);*/
    }
//stackBlurCanvasRGBA
//stackBlurCanvasRGB
//?stackBoxBlurCanvasRGBA
//stackBlurCanvasRGBA( 'myCanvas', 0, 0, canvasW, canvasH, 2, 2 );

var dataURL = canvas.toDataURL();
document.getElementById('renderedImage').src = dataURL;
//}, 1000);
}, true);
/*
Usage: stackBoxBlurImage( sourceImageID, targetCanvasID, radius, blurAlphaChannel, iterations );
or: stackBoxBlurCanvasRGBA( targetCanvasID, top_x, top_y, width, height, radius, iterations );
or: stackBoxBlurCanvasRGB( targetCanvasID, top_x, top_y, width, height, radius, iterations );
*/
    </script>
  </div>
  </body>
</html>
