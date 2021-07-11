<div class="mb-20 bg-green-50 rounded-lg p-4 border border-green-400">
	<div class="flex pb-5 gap-1 w-full">
		<div class="flex-1">
			<label for="periode_de">Date Début</label>
			<input type="date" id="periode_de" value="<?= isset($ppl)? $ppl["de"]: "2021-01-01" ?>">
			<?php
				if(isset($ppl))
					echo '<input type="hidden" id="id" value="'.$ppl["id"].'">'
			?>
		</div>
		<div class="flex-1">
			<label for="periode_a">Date Fin </label>
			<input type="date" id="periode_a" value="<?= isset($ppl)? $ppl["a"]: "2021-12-31" ?>">
		</div>
		<div class="w-24">
			<label for="periode_nuite">Nuités </label>
			<input type="number" id="periode_nuite" value="<?= isset($ppl)? $ppl["nbr_nuite"]: "365" ?>" style="font-size: 16px; text-align: center; background-color: rgba(249,245,191,1.00)">
		</div>
	</div>
	<div class="flex gap-1 pb-10">
		<div class="w-30">
			<label for="periode_montant">Montant</label>
			<input type="number" id="periode_montant" value="<?= isset($ppl)? $ppl["montant"]: "" ?>">
		</div>
		<div class="flex-1">
			<label for="date_fin">Type </label>
			<select id="ppl_type">
				<option selected value="-1"></option>
				<option <?= isset($ppl)? ($ppl["id_propriete_location_type"] === "1"? "selected": ""): "" ?> value="1">Par Nuit</option>
				<option <?= isset($ppl)? ($ppl["id_propriete_location_type"] === "2"? "selected": ""): "" ?> value="2">Par Mois</option>
				<option <?= isset($ppl)? ($ppl["id_propriete_location_type"] === "3"? "selected": ""): "" ?> value="3">Forfait</option>
			</select>
		</div>
	</div>
	<div class="row pb-10">
		<div class="col_12 d-flex">
			<div>
				<label class="switch" style="width: 40px">
					<input class="field" id="status" type="checkbox" <?= isset($ppl)? ($ppl["status"] === "1"? "checked": ""): "" ?>>
					<span class="slider round"></span>
				</label>
			</div>
			<div class="pt-5 pl-5"> Activé</div>
		</div>	
	</div>

	<div class="row pb-10 pt-10">
		<div class="col_12 d-flex">
			<button class="blue store_1 mr-10" value="<?= isset($ppl)? $ppl["id_propriete"]: $id_propriete ?>">Enregistrer</button>
			<?php
				if(isset($ppl))
					echo '<button class="ppl_delete red mr-10" value="'.$ppl["id"].'">Supprimer</button>';
			?>
			<button class="abort_1">Annuler</button>
		</div>	
	</div>

</div>
<script>
    $(document).ready(function(){
        $('.abort_1').on('click', function(){
            $('.create_1').removeClass('hide');
            $('.add_container_1').html("");
        });

        $('.store_1').on('click', function(){
            
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