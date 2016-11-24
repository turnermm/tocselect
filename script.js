
 
 jQuery(document).ready(function() {
     jQuery( "#selectoc_btn" ).click(function() { 
       var file = this.form.selectoc_id.value;        
       var params = "seltoc_val=" +  encodeURIComponent(file);   
       params += '&call=tocselect';     
     
        jQuery.post( DOKU_BASE + 'lib/exe/ajax.php',  params,
                function (data) {  
                if(!data) {
                    alert ('No TOC for ' + file);
                }
                else if (data == 'E_FNF') {
                    alert (file + " not found");
                }
                 else  document.getElementById("setctoc_out").innerHTML = data;                           
                    },
                'html'
            );      
     
   });     
 });
 