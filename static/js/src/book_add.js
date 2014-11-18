$(document).ready(function(){
   $(document).on("submit", "#genre_registration", function(e){
      $form = $(this);
      $.post($form.attr("action"), $form.serialize(), function(response){
         $genre_opt = $(".select_genre");
         $opt_group = undefined;
         $genre_opt.empty();
         $.each(response, function(data){
            if(this.genre_id%100 == 0){
               if($opt_group != undefined)
                  $genre_opt.append($opt_group);
               $opt_group = $("<optgroup></optgroup>");
               $opt_group.attr("label", this.genre);
            }
            $opt = $("<option></option>");
            $opt.val(this.genre_id);
            $opt.text(this.genre_id+" - "+this.genre);
            $opt_group.append($opt);
         });
         if($opt_group != undefined)
            $genre_opt.append($opt_group);
         $('#genre_add_modal').modal('hide');
      });
      return false;
   });
});
