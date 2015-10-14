/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$(document).ready(function(){
    function displayPlanning() {
        var company = $("#form_filter_company").val();
        var cp = $("#form_filter_cp").val();
        var project = $("#form_filter_project").val();
        var das = $("#form_filter_das").val();
        var solution = $("#form_filter_solution").val();
        $.ajax({
            type: 'POST',
            url: Routing.generate('asmolding_admin_filterByCriteria', {'companySeparator':'/','companyId':company, 'contactSeparator':'/', 'contactId': cp, 'projectSeparator': '/', 'projectId': project, 'dasSeparator': '/', 'das': das, 'solutionSeparator': '/', 'solution':solution}),
            dataType: 'html',
            //data: DATA,
            cache: false,
            success:function (html){
                $('#results').html(html);
            },
            error: function(html){
                $('#results').html(html);
            }
        });
        return false;
    };
//});
