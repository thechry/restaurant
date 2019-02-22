$(document).ready(function()
{
    // Εμφανίζει το όνομα του logo στο input tag
    $('#inputLogoImg').on('change',function(){
       //get the file name
       var fileName = $(this).val();
       //replace the "Choose a file" label
       $(this).next('.custom-file-label').html(fileName);
   });

    // Εμφανίζει το όνομα του logo στο input tag
    $('#inputCategoryImg').on('change',function(){
       //get the file name
       var fileName = $(this).val();
       //replace the "Choose a file" label
       $(this).next('.custom-file-label').html(fileName);
   });
   
   // Εμφανίζει το όνομα του logo στο input tag
    $('#inputSubcategoryImg').on('change',function(){
       //get the file name
       var fileName = $(this).val();
       //replace the "Choose a file" label
       $(this).next('.custom-file-label').html(fileName);
   });
   
      // Εμφανίζει το όνομα του logo στο input tag
    $('#inputProductImg').on('change',function(){
       //get the file name
       var fileName = $(this).val();
       //replace the "Choose a file" label
       $(this).next('.custom-file-label').html(fileName);
   });
   

    $("#form_page input[type=text]").hover(function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $('#form_page input[type=text]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });
    

    $("#signin_form input[type=text]").hover(function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $('#signin_form input[type=text]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });
                
    $("#signin_form input[type=password]").hover(function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $('#signin_form input[type=password]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $("#signup_form input[type=text]").hover(function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });
                
    $('#signup_form input[type=text]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $("#signup_form input[type=password]").hover(function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });
                
    $('#signup_form input[type=password]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $("#page_settings_form input[type=file]").on('change invalid', function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $('#page_settings_form input[type=file]').hover(function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if(!textfield.validity.valid) {
            textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');
        }
    });
                
    $("#page_settings_form input[type=text]").hover(function(){
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $('#page_settings_form input[type=text]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    // Create thumbnail of image from input file
    $('#inputLogoImg').on('change', function(){ //on file input change
        if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
    	{
            $('#thumb-output').html(''); //clear html of output element
            var data = $(this)[0].files; //this file data
            $.each(data, function(index, file){ //loop though each file
                if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                    var fRead = new FileReader(); //new filereader
                    fRead.onload = (function(file){ //trigger function on successful read
                        return function(e) {
                            var img = $('<img/>').addClass('thumb').attr('src', e.target.result).attr('width', '75px'); //create image element 
                            $('#thumb-output').append(img); //append image to output element
                        };
                    })(file);
                    fRead.readAsDataURL(file); //URL representing the file's data.
                }
            });
        }else{
            alert("Your browser doesn't support File API!"); //if File API is absent
        }
    });

    // Create thumbnail of image from input file
    $('#inputCategoryImg').on('change', function(){ //on file input change
        if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
    	{
            $('#thumb-cat').html(''); //clear html of output element
            var data = $(this)[0].files; //this file data
            $.each(data, function(index, file){ //loop though each file
                if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                    var fRead = new FileReader(); //new filereader
                    fRead.onload = (function(file){ //trigger function on successful read
                        return function(e) {
                            var img = $('<img/>').addClass('thumb').attr('src', e.target.result).attr('width', '75px'); //create image element 
                            
                            $('#thumb-cat').append(img); //append image to output element
                        };
                    })(file);
                    fRead.readAsDataURL(file); //URL representing the file's data.
                }
            });
        }else{
            alert("Your browser doesn't support File API!"); //if File API is absent
        }
    });
    
    // Create thumbnail of image from input file
    $('#inputSubcategoryImg').on('change', function(){ //on file input change
        if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
    	{
            $('#thumb-subcat').html(''); //clear html of output element
            var data = $(this)[0].files; //this file data
            $.each(data, function(index, file){ //loop though each file
                if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                    var fRead = new FileReader(); //new filereader
                    fRead.onload = (function(file){ //trigger function on successful read
                        return function(e) {
                            var img = $('<img/>').addClass('thumb').attr('src', e.target.result).attr('width', '75px'); //create image element 
                            
                            $('#thumb-subcat').append(img); //append image to output element
                        };
                    })(file);
                    fRead.readAsDataURL(file); //URL representing the file's data.
                }
            });
        }else{
            alert("Your browser doesn't support File API!"); //if File API is absent
        }
    });
    
    // Create thumbnail of image from input file
    $('#inputProductImg').on('change', function(){ //on file input change
        if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
    	{
            $('#thumb-prod').html(''); //clear html of output element
            var data = $(this)[0].files; //this file data
            $.each(data, function(index, file){ //loop though each file
                if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                    var fRead = new FileReader(); //new filereader
                    fRead.onload = (function(file){ //trigger function on successful read
                        return function(e) {
                            var img = $('<img/>').addClass('thumb').attr('src', e.target.result).attr('width', '75px'); //create image element 
                            
                            $('#thumb-prod').append(img); //append image to output element
                        };
                    })(file);
                    fRead.readAsDataURL(file); //URL representing the file's data.
                }
            });
        }else{
            alert("Your browser doesn't support File API!"); //if File API is absent
        }
    });


    /* Not working
    $('#form_page input[type=text]').on('change invalid', function() {
        var textfield = $(this).get(0);
        //alert("test change");
        textfield.setCustomValidity('');
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });

    $("#form_page input[type=text]").hover(function(){
        var textfield = $(this).get(0);
         textfield.setCustomValidity('');
        //alert("test hover");
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
        }
    });
    */
});
            /*
            $('#signup_form input[type=text]').on('change invalid', function() {
                var textfield = $(this).get(0);
    
                // 'setCustomValidity not only sets the message, but also marks
                // the field as invalid. In order to see whether the field really is
                // invalid, we have to remove the message first
                textfield.setCustomValidity('');

                if (!textfield.validity.valid) {
                  textfield.setCustomValidity('Το πεδίο είναι υποχρεωτικό');  
                }
            });
            */
           
function fetch_select(val) {
    $.ajax({
        type: 'get',
        url: 'http://localhost/restaurant-slim-v1/public/admin/dashboard/product/subcatSelect',
        data: { get_option:val },
        success: function (response) {
            document.getElementById("selectsubcategory").innerHTML=response; 
        }
    });
}           
           
function postPageForm() {
     var x = document.getElementsByName('page_form');
     x[0].submit();
 }

function postCategoryForm() {
     var cat = document.getElementsByName('category_form');
     cat[0].submit();
 }
 
 function postSubcategoryForm() {
     var subcat = document.getElementsByName('subcategory_form');
     subcat[0].submit();
 }

 function postProductForm() {
     var prod = document.getElementsByName('product_form');
     prod[0].submit();
 }

// Load english translation
function ref_en() {
    var x = document.getElementById("en_menu");
    var url = window.location.href;
    var ref_en = url.replace("/gr", "/en");
    x.setAttribute("href", ref_en);
}

//Load greek translation
function ref_gr() {
    var x = document.getElementById("gr_menu");
    var url = window.location.href;
    var ref_en = url.replace("/en", "/gr");
    x.setAttribute("href", ref_en);
}

/*
function postDeleteCategoryForm() {
    alert("button pressed");
    var checkedValue = null; 
    var inputElements = document.getElementsByName('delete_category_form');
    //$("#delete_category").change(function() {
     //$("form").submit();
   //});
    
    for(var i=0; inputElements[i]; ++i){
        if(inputElements[i].checked){
            checkedValue = inputElements[i].value;
            alert(checkedValue);
            //inputElements[i].submit();
        }
        
    }
*/


           
function open_translate(elmnt) {
     var a = document.getElementById("google_translate_element");
     if (a.style.display == "") {
         a.style.display = "none";
         elmnt.innerHTML = '<i class="fas fa-globe-africa text-light" >';
     } else {
         a.style.display = "";
         if (window.innerWidth > 500) {
             a.style.width = "20%";
         } else {
             a.style.width = "100%";
         }
         elmnt.innerHTML = "<span style='font-family:verdana;font-weight:bold;'>X</span>";
     }
 }
            

            
$(document).ready(function(){
    if($('a').hasClass("active")) {
        $(this).addClass("");
        $('a').click(function(){    
            $('a').removeClass("active");
            $(this).addClass("active");
        });
    }
});
            
function writeUpper() {
    var x = document.getElementById("inputPackage");
    var y = document.getElementById("productDesc");
    var z = document.getElementById("editedPackage");
    if(x) {
            x.value = x.value.toUpperCase();
    }				

    if(y) {
            y.value = y.value.toUpperCase();
    }

    if(z) {
            z.value = z.value.toUpperCase();
    }
}
            
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});