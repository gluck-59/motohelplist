// смена аватара    
function avatar_change()
{
//   $('#avatar_upload').toggle(300);
}

$$('#avatar').onclick = function(){
    $$('#upload').click();
}

var $uploadCrop;
function readFile(input) 
{
	if (input.files && input.files[0]) 
	{
        var reader = new FileReader();
        reader.onload = function (e) 
        {
        	$uploadCrop.croppie('bind', {
        		url: e.target.result
        	});
//        	$('.upload-demo').addClass('ready');
        }
        reader.readAsDataURL(input.files[0]);
    }
    else 
    {
        alert("Sorry - you need to update your OS");
    }
}

$uploadCrop = $('#upload-demo').croppie({
	viewport: {
		width: 100,
		height: 100//,
//		type: 'circle'
	},
	boundary: {
		width: 300,
		height: 300
	}
});

$('#upload').on('change', function () { readFile(this); });
$('.upload-result').on('click', function (ev) 
{
	$uploadCrop.croppie('result', 'canvas').then(function (resp) 
	{
        resp = resp.toDataURL("image/jpeg", 0.8);
        
        $('#avatar').attr('src', resp);

        // Creating object of FormData class
        var form_data = new FormData();                  
    	form_data.append("file", resp)
    				
        var jqxhr = $.ajax(
        {
            url: "/avatar_upload.php",
            type: "POST",
            dataType: 'json', 
            data: form_data,
            processData: false,
            contentType: false
        })
        jqxhr.complete(function(res){ 
            ohSnap(res.responseText)
        });        
    });
});    