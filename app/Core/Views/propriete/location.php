<div id="popup" class="cursor-default 2xl:w-1/2 xl:w-2/3 md:w-10/12 w-11/12">	

	<div class="popup-header d-flex space-between">
		<div class="">Propriete Reservations</div>
		<div class="red-text"><button class="modal_close"><i class="fas fa-times"></i></button></div>
	</div>

	<div class="popup-content text-left" style="padding-bottom: 25px;">
        <div class="flex gap-2">
            <div class="flex-1">
                <div class="flex justify-between items-center mb-10">
                    <div class="font-bold text-blue-600">Contrats envers Propriétaire</div>
                    <div class="flex">
                        <button class="green create_1" value="<?= $id_propriete ?>"><i class="fas fa-plus"></i> Ajouter</button>
                        <button class="refresh_1 hide" value="<?= $id_propriete ?>"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
                <div class="add_container_1"></div>
                <table class="w-full table_1">
                    <thead>
                        <tr class="bg-blue-300 rounded">
                            <th class="py-1 text-gray-600 text-sm px-1">PERIODE</th>
                            <th class="py-1 text-gray-600 text-sm px-1 text-center">TYPE</th>
                            <th class="py-1 text-gray-600 text-sm px-1 text-center">MONTANT</th>
                            <th class="py-1 text-gray-600 text-sm px-1 text-center">STATUS</th>
                            <th class="py-1 text-gray-600 text-sm px-1 text-center"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($ppl as $p): ?>
                            <?php include('proprietaire_location/tr.php'); ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-center mb-10">
                    <div class="font-bold text-green-600">Contrats envers Client</div>
                    <div class="flex">
                        <button class="green create_2" value="<?= $id_propriete ?>"><i class="fas fa-plus"></i> Ajouter</button>
                        <button class="refresh_2 hide" value="<?= $id_propriete ?>"><i class="fas fa-sync-alt"></i></button>
                        <button class="abort_2 hide bg-red-500"><i class="fas fa-times"></i> Annuler</button>
                    </div>
                </div>
                <div class="add_container_2"></div>
                <table class="w-full table_2">
                    <thead>
                        <tr class="bg-green-300 rounded">
                            <th class="py-1 text-gray-600 text-sm px-1">DEBUT</th>
                            <th class="py-1 text-gray-600 text-sm px-1 text-center">CLIENT</th>
                            <th class="py-1 text-gray-600 text-sm px-1 text-center">STATUS</th>
                            <th class="py-1 text-gray-600 text-sm px-1 text-center"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($pl as $p): ?>
                            <?php include('client_location/tr.php'); ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
	</div>
</div>
<script>
    $(document).ready(function(){
        $('.refresh_1').on('click', function(){
            var data = {
                'controler'		:	'Propriete',
                'function'		:	'get_proprietaire_locations_by_propriete',
                'params'		:	{
                    'id_propriete'		:	$(this).val()
                }
            };
            $.ajax({
                type		: 	"POST",
                url			: 	"pages/default/ajax/ajax.php",
                data		:	data,
                dataType	: 	"json",
            }).done(function(response){
                $('.table_1 tbody').html(response.msg)
                
            }).fail(function(xhr) {
                alert("Error");
                console.log(xhr.responseText);
            });
        });

        $('.create_1').on('click', function(){
            $('.create_1').addClass('hide');
            var data = {
                'controler'		:	'Propriete',
                'function'		:	'create_proprietaire_locations',
                'params'		:	{
                    'id_propriete'		:	$(this).val()
                }
            };
            $.ajax({
                type		: 	"POST",
                url			: 	"pages/default/ajax/ajax.php",
                data		:	data,
                dataType	: 	"json",
            }).done(function(response){
                $('.add_container_1').html(response.msg);
                
            }).fail(function(xhr) {
                alert("Error");
                console.log(xhr.responseText);
            });
        });

        $('.update_1').on('click', function(){
            $('.create_1').addClass('hide');
            var data = {
                'controler'		:	'Propriete',
                'function'		:	'update_proprietaire_locations',
                'params'		:	{
                    'id'		:	$(this).val()
                }
            };
            console.log(data);
            $.ajax({
                type		: 	"POST",
                url			: 	"pages/default/ajax/ajax.php",
                data		:	data,
                dataType	: 	"json",
            }).done(function(response){
                $('.add_container_1').html(response.msg);
                
            }).fail(function(xhr) {
                alert("Error");
                console.log(xhr.responseText);
            });
        });


        $('.create_2').on('click', function(){
            $('.create_2').addClass('hide');
            $('.abort_2').removeClass('hide');
            var data = {
                'controler'		:	'Propriete',
                'function'		:	'create_client_locations',
                'params'		:	{
                    'id_propriete'		:	$(this).val()
                }
            };
            $.ajax({
                type		: 	"POST",
                url			: 	"pages/default/ajax/ajax.php",
                data		:	data,
                dataType	: 	"json",
            }).done(function(response){
                $('.add_container_2').html(response.msg);
                
            }).fail(function(xhr) {
                alert("Error");
                console.log(xhr.responseText);
            });
        });

        $('.abort_2').on('click', function(){
            $('.create_2').removeClass('hide');
            $(this).addClass('hide');
            $('.add_container_2').html("");
        });
    });
</script>
