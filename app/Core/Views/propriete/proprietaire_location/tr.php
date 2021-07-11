<?php
	$type = 'Par Nuit';
	$type = ($p["id_propriete_location_type"] === "1")? $type: ($p["id_propriete_location_type"] === "2"? "Par Mois": "Forfait");
	$status = ($p["status"] === "1")? "<div class='rounded-lg bg-green-200 px-2 py-1'>Activé</div>": "<div class='rounded-lg bg-red-300 px-2 py-1'>Archivé</div>";
?>
<tr class="border-b text-xs lg:text-sm hover:bg-gray-100">
    <td class="w-32 py-1">
        <div class="flex items-center gap-2">
            <div class="text-xs">
                <div><?= $p["de"] ?></div> 
                <div><?= $p["a"] ?></div>
            </div>
            <div class="flex-1 text-red">[ <?= $p["nbr_nuite"] ?> ]</div>
        </div>
    </td>
    <td class="py-1 text-center text-xs"><?= $type ?></td>
    <td class="py-1 text-center text-xs"><?= $Obj->format($p["montant"]) ?></td>
    <td class="py-1 text-center text-xs"><?= $status ?></td>
    <td class="py-1 text-xs" style="width:50px; text-align:center">
        <button class="update_1" value="<?= $p['id'] ?>">
            <i class="fas fa-ellipsis-v"></i>
        </button>
    </td>
</tr>