/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(".loading").hide();
    $("#formSearchCompanies").submit(function(){ 
        $(".loading").show();
        var motcle = $("#form_search_name").val();
        alert(motcle);
        var DATA = 'search:' + motcle;
        alert(DATA);
        $.ajax({
            type: "POST",
            url: "{{ path('asmolding_admin_searchCompaniesAjax') }}",
            data: DATA,
            cache: false,
            success: function(data){
               $('#results').html(data);
               $(".loading").hide();
            },
            error: function(){
               alert('ERROR AJAX');
            }
        });    
        return false;
    });

