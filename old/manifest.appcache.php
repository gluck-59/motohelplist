<?php
header('Content-Type: text/cache-manifest');
$filesToCache = array(
'sass/templates.css',
'sass/croppie.css',
'js/jquery.min.js',
'js/sha1.js',
'js/ohsnap.js',
'js/foundation.min.js',
'js/foundation.offcanvas.js',
'js/jquery-ui.min.js',
'js/cookie.js',
'js/croppie.js',
'js/tools.min.js',
'js/geometa.min.js',
'js/gmaps.min.js',
'js/markerclusterer_compiled.js',
'js/jquery.ui.autocomplete.min.js',
'js/moment.min.js',
'js/helplist.min.js',
'js/chat-personal.min.js',
'fonts/PTS55F_W.eot',
'fonts/PTS55F_W.svg',
'fonts/PTS55F_W.ttf',
'fonts/PTS55F_W.woff',
'fonts/PTS56F_W.eot',
'fonts/PTS56F_W.woff',
'fonts/PTS56F_W.ttf',
'fonts/PTS56F_W.svg',
'img/arrow_back.png',
'img/logo_1024.png',
'img/ui-anim_basic_16x16.gif',
'new_unread_message.mp3',
'new_chat_message.mp3',
'login.html',
'helplist.html'
);
?>
CACHE MANIFEST

CACHE:
<?php
// Print files that we need to cache and store hash data
$hashes = '';
foreach($filesToCache as $file) {
    echo $file."\n";
    $hashes.=md5_file($file);
};
?>

#SETTINGS:
#prefer-online

NETWORK:
*

FALLBACK:
/ helplist.html

# Hash Version: <?=md5($hashes)?>

