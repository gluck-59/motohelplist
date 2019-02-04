function herak() {
  transport = new XMLHttpRequest();
  transport.open('GET', 'https://app.motohelplist.com/online.php', true);
  transport.onreadystatechange = function(){
    if(transport.readyState == 4){
      postMessage(transport.response);
    } 
  };
  transport.send();
};


setInterval(function(){ herak()}, 2000);