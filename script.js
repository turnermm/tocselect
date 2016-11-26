
 
 jQuery(document).ready(function() {
     jQuery( "#selectoc_btn" ).click(function() { 
       var file = this.form.selectoc_id.value;        
       var params = "seltoc_val=" +  encodeURIComponent(file);   
       params += '&call=tocselect';     
     
        jQuery.post( DOKU_BASE + 'lib/exe/ajax.php',  params,
                function (data) {  
                if(!data) {
                        document.getElementById("setctoc_out").innerHTML = "";                    
                    alert ('No TOC for ' + file);
                }
                else if (data == 'E_FNF') {
                        document.getElementById("setctoc_out").innerHTML = "";                    
                    alert (file + " not found");
                }
                 else  document.getElementById("setctoc_out").innerHTML = data;                           
                    },
                'html'
            );      
   }); 
     var cval = getCookie('tocselect');
     if(cval && document.getElementById("selectoc_id")) {
         cval = cval.replace(/%3A/g,':');
     
        document.getElementById("selectoc_id").value = cval;
        jQuery( "#selectoc_btn" ).click();
     }
 });
 
 function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
} 