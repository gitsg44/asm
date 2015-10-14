/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

  $(function() {
    $( "#sortable" ).sortable({
        placeholder: "ui-state-highlight",
        update: function (event,ui){
            var list = ui.item.parent("ul");
            var pos = 0;
            $(list.find("li")).each(function(){
                pos++;
                $(this).find("input#posInput").val(pos);
            });
        }
    });
  });
