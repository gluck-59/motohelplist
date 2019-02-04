// Initialize your app


var myApp = new Framework7(
    {
    }
);

// Export selectors engine
var $$ = Dom7;

var mainView = myApp.addView('.view-main', 
{
//    pushState : true,
//    pushStateRoot : 'app.motohelplist.com/f7/dist/',
    //animatePages:false,
});


//! функции document.ready
$$(document).on('DOMContentLoaded', function()
{

    // фотки для photoBrowser
    //photoBrowserIndex = -1;


//    if (myApp.ls.getItem('token'))
//    {
        //************************************************ первая страница которая будет загружена *********************************************************************************/

        myApp.mainView.loadPage('map.html');






/////////////////////////////////

});





myApp.onPageInit('map', function (page) 
{

Map = new GMaps({
  div: '#map-canvas',
  lat: -12.043333,
  lng: -77.028333
});

});

//! профиль стр
