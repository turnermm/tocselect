
 
 jQuery(document).ready(function() {
     jQuery( "#selectoc_btn" ).click(function() { 
     
        var params = "seltoc_val=" +  encodeURIComponent(this.form.selectoc_id.value);   
       params += '&call=tocselect';
      // params = encodeURIComponent(params);
   //    alert(params);
        jQuery.post( DOKU_BASE + 'lib/exe/ajax.php',  params,
                function (data) {  
                    document.getElementById("setctoc_out").innerHTML = data;
                    alert(data);             
                    },
                'html'
            );      
     
   });     
 });
 