<div class="mb-20 bg-green-50 rounded-lg p-4 border border-green-400">
    <div class="row">
        <div class="col_12">
            <div class="form-element">
                <label for="client">Client</label>
                <select class="client" data-id_propriete="<?= $id_propriete ?>">
                    <option selected value="-1">-- Clients </option>
                    <?php
                        foreach($clients as $client){
                            echo '<option data-id="'.$client["id_client"].'" value="' . $client["contrat_UID"].'">' . $client["societe_name"] . ' : ' . $client["annee"] . '</option>';
                        }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="container_1" style="max-height: 125px; overflow: auto"></div>


</div>
<script>
    $(document).ready(function(){
        $('.abort_1').on('click', function(){
            $('.create_1').removeClass('hide');
            $('.add_container_1').html("");
        });

        $('.client').on('change', function(){
            var element = $(this).find('option:selected');
            var data = {
                'controler'		:	'Propriete',
                'function'		:	'get_periodes_by_client',
                'params'		:	{
                    'UID'			:	$(this).val(),
                    'id_propriete'	:	$(this).attr('data-id_propriete'),
                    'id_client'		:	element.attr('data-id'),
                }
            };

            $.ajax({
                type		: 	"POST",
                url			: 	"pages/default/ajax/ajax.php",
                data		:	data,
                dataType	: 	"json",
            }).done(function(response){
                $('.container_1').html(response.msg);
            }).fail(function(xhr) {
                alert("Error");
                console.log(xhr.responseText);
            });
        });

        $('.store_2').on('click', function(){
            
            var continu = true;
            
            if($("#periode_nuite").val() === "" || $("#periode_nuite").hasClass("error")){
                continu = false;
                $("#periode_nuite").addClass("error");
            }else{
                $("#periode_nuite").removeClass("error");
            }
            
            if($("#periode_montant").val() === "" || $("#periode_montant").val() === 0){
                continu = false;
                $("#periode_montant").addClass("error");
            }else{
                $("#periode_montant").removeClass("error");
            }
            
            if($("#ppl_type").val() === "-1"){
                continu = false;
                $("#ppl_type").addClass("error");
            }else{
                $("#ppl_type").removeClass("error");
            }
            
            if(continu){
                
                var columns = {
                    'de'							:	$("#periode_de").val(),
                    'a'								:	$("#periode_a").val(),
                    'montant'						:	$("#periode_montant").val(),
                    'id_propriete'					:	$(this).val(),
                    'status'						:	$("#status").is(':checked')? 1: 0,
                    'id_propriete_location_type'	:	$("#ppl_type").val(),
                };
                
                if($("#id").length > 0){
                    columns.id = $("#id").val();
                }
                
                var data = {
                    'controler'		:	'Propriete_Proprietaire_Location',
                    'function'		:	'Store',
                    'params'		:	columns
                };

                $.ajax({
                    type		: 	"POST",
                    url			: 	"pages/default/ajax/ajax.php",
                    data		:	data,
                    dataType	: 	"json",
                }).done(function(response){

                    $('.refresh_1').trigger('click');
                    $('.abort_1').trigger('click');

                }).fail(function(xhr) {
                    alert("Error");
                    console.log(xhr.responseText);
                });
                            
            }
            

            
        });

    });
</script>
