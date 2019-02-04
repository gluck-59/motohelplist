<?php
header('Content-Type: text/cache-manifest');
$filesToCache = array(
'css/framework7.ios.min.css',
'css/framework7.ios.colors.min.css',
'css/styles.css',
'css/croppie.css',

//'js/jquery.min.js',

'js/framework7.min.js',
'js/main.min.js',
'js/load_worker.js',
'js/helplist.min.js',
'js/onair_digest.min.js',
'js/feed.min.js',
'js/subscribe.js',
'js/friends.min.js',
'js/croppie.min.js',
'js/messages.js',
'js/helplist_worker.js',
'js/moment.min.js',
'js/sha1.min.js',
'js/gmaps.min.js',
'js/geometa.min.js',
'js/markerclusterer_compiled.js',
'js/Detect.js',

'index.html',
'onair.html',
'feed.html',
'friends.html',
'profile.html',
'profile_view.html',
'settings.html',
'subscribe.html',
'registration.html',
'map.html',
'trip.html',
'help_list.html',
'channel_info.html',
'donate.html',
'en.json',
'ru.json',

'img/city.png',
'img/cycle.png',
'img/group.png',
'img/loader.gif',
'img/i-form-active-ios.svg',
'img/i-form-bed-ios.svg',
'img/i-form-beer-ios.svg',
'img/i-form-calendar-ios.svg',
'img/i-form-city-ios.svg',
'img/i-form-comment-ios.svg',
'img/i-form-cycle-ios.svg',
'img/i-form-cyclemore-ios.svg',
'img/i-form-email-ios.svg',
'img/i-form-excursion-ios.svg',
'img/i-form-exit-ios.svg',
'img/i-form-food-ios.svg',
'img/i-form-friends-ios.svg',
'img/i-form-garage-ios.svg',
'img/i-form-gender-ios.svg',
'img/i-form-group-ios.svg',
'img/i-form-help-ios.svg',
'img/i-form-helplist-ios.svg',
'img/i-form-incognito-ios.svg',
'img/i-form-name-ios.svg',
'img/i-form-news-ios.svg',
'img/i-form-party-ios.svg',
'img/i-form-password-ios.svg',
'img/i-form-private-ios.svg',
'img/i-form-repair-ios.svg',
'img/i-form-settings-ios.svg',
'img/i-form-strong-ios.svg',
'img/i-form-tel-ios.svg',
'img/i-form-toggle-ios.svg',
'img/i-form-url-ios.svg',
'img/i-tabbar-channels-active-ios.svg',
'img/i-tabbar-channels-add-active-ios.svg',
'img/i-tabbar-channels-add-ios.svg',
'img/i-tabbar-channels-ios.svg',
'img/i-tabbar-search-active-ios.svg',
'img/i-tabbar-search-ios.svg',
'img/logo_1024.png',
'img/logo_196.png',
'img/logo_big.png',
'img/pattern1.png',
'img/private.png',
'img/trip.png',
'img/tripim.png',
'img/tripfriend.png',
'img/i-donate-ios.svg',
'img/i-bug-ios.svg',
'img/i-settings-ios.svg',
'img/i-addtohomescreen-ios.svg',
'img/i-addtohomescreen1-ios.svg',

/*
'img/i-form-active-material.svg',
'img/i-form-bed-material.svg',
'img/i-form-beer-material.svg',
'img/i-form-calendar-material.svg',
'img/i-form-city-material.svg',
'img/i-form-comment-material.svg',
'img/i-form-cycle-material.svg',
'img/i-form-cyclemore-material.svg',
'img/i-form-email-material.svg',
'img/i-form-excursion-material.svg',
'img/i-form-exit-material.svg',
'img/i-form-food-material.svg',
'img/i-form-friends-material.svg',
'img/i-form-garage-material.svg',
'img/i-form-gender-material.svg',
'img/i-form-group-material.svg',
'img/i-form-help-material.svg',
'img/i-form-helplist-material.svg',
'img/i-form-incognito-material.svg',
'img/i-form-name-material.svg',
'img/i-form-news-material.svg',
'img/i-form-party-material.svg',
'img/i-form-password-material.svg',
'img/i-form-private-material.svg',
'img/i-form-repair-material.svg',
'img/i-form-settings-material.svg',
'img/i-form-strong-material.svg',
'img/i-form-tel-material.svg',
'img/i-form-toggle-material.svg',
'img/i-form-url-material.svg',
'img/i-donate-material.svg',
'img/i-bug-material.svg',
*/

'new_chat_message.mp3',
'new_unread_message.mp3'
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
$hash = md5($hashes);
?>

SETTINGS:
prefer-online

NETWORK:
*

# FALLBACK:
#/ index.html

# Hash Version: <?=$hash?> ss