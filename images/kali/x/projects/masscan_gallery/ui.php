<?php

//if (isset($_GET['preview'])) {
//    $file = './preview/' . $_GET['preview'] . '/index.html';
//    $page = file_get_contents($file);
//    echo $page;
//    exit;
//}

//$filename = 'out_masscan2';
//$lines = explode("\n", file_get_contents($filename));
//foreach ($lines as $i => $line) {
//    $parts = explode(" ", $line);
//    if (isset($parts[3])) {
//        $ip = $parts[3];
//        $frame = sprintf("<iframe class='block' src='?preview=%s'></iframe>", $ip);
//        $link = sprintf("<a href='http://%s' target='_blank'>%s</a>", $ip, $ip);
//        echo sprintf("<div class='holder'><div class='bar'>%s</div>%s</div>", $link, $frame);
//    }
//}

echo '
<div id="loader">
  <div class="inner rotate-one"></div>
  <div class="inner rotate-two"></div>
  <div class="inner rotate-three"></div>
</div>
';

foreach (glob('./pages/*') as $file) {
    $file = 'http://192.168.123.26:8000' . str_replace('./', '/', $file);
    $frame = sprintf("<img class='lazy block' data-src='%s'/>", $file);
    $link = sprintf("<a href='http://%s' target='_blank'>%s</a>", $file, $file);
    echo sprintf("<div class='holder'><div class='bar'>%s</div>%s</div>", $link, $frame);
}

echo '
document.addEventListener("DOMContentLoaded", function() {
  var lazyloadImages;    

  if ("IntersectionObserver" in window) {
    lazyloadImages = document.querySelectorAll(".lazy");
    var imageObserver = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var image = entry.target;
          image.src = image.dataset.src;
          image.classList.remove("lazy");
          imageObserver.unobserve(image);
        }
      });
    });

    lazyloadImages.forEach(function(image) {
      imageObserver.observe(image);
    });
  } else {  
    var lazyloadThrottleTimeout;
    lazyloadImages = document.querySelectorAll(".lazy");
    
    function lazyload () {
      if(lazyloadThrottleTimeout) {
        clearTimeout(lazyloadThrottleTimeout);
      }    

      lazyloadThrottleTimeout = setTimeout(function() {
        var scrollTop = window.pageYOffset;
        lazyloadImages.forEach(function(img) {
            if(img.offsetTop < (window.innerHeight + scrollTop)) {
              img.src = img.dataset.src;
              img.classList.remove("lazy");
            }
        });
        if(lazyloadImages.length == 0) { 
          document.removeEventListener("scroll", lazyload);
          window.removeEventListener("resize", lazyload);
          window.removeEventListener("orientationChange", lazyload);
        }
      }, 20);
    }

    document.addEventListener("scroll", lazyload);
    window.addEventListener("resize", lazyload);
    window.addEventListener("orientationChange", lazyload);
  }
})
';
?>

<style>
    .holder {
        width: 600px;
        height: 400px;
        position: relative;
        display: inline-block;
    }

    .block {
        width: 100%;
        height: 100%;
    }

    .bar {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 40px;
    }

    /**
            LOADER
     */
    #loader{
        width:100px;
        height:80px;
        position:fixed;
        top:50%;
        left:50%;
        margin-top:-40px;
        margin-left:-50px;
    }

    .inner{
        -webkit-animation:rotate 0.8s linear infinite;
        -moz-animation:rotate 0.8s linear infinite;
        animation:rotate 0.8s linear infinite;
        margin-top:20px;
        border-radius:50%;
        width:50px;
        height:50px;
        position:absolute;
    }

    .rotate-one{
        left:50%;
        margin-left:-25px;
        border-top:2px solid #1f6d8f;
    }

    .rotate-two{
        top:-10px;
        left:30px;
        border-left:2px solid #1f6d8f;
    }

    .rotate-three{
        top:-10px;
        right:30px;
        border-right:2px solid #1f6d8f;
    }

    @-webkit-keyframes rotate{
        0%{-webkit-transform:rotateZ(0deg);}
        100%{-webkit-transform:rotateZ(360deg);}
    }

    @-moz-keyframes rotate{
        0%{-moz-transform:rotateZ(0deg);}
        100%{-moz-transform:rotateZ(360deg);}
    }

    @keyframes rotate{
        0%{transform:rotateZ(0deg);}
        100%{transform:rotateZ(360deg);}
    }
</style>


