<?php session_start(); $core = $_SESSION['CORE']; 

$table_name = $_POST["page"];
require_once($core.$table_name.".php");
$ob = new $table_name();
$ob->id = $_POST["id"];
$data = $ob->read()[0];

$formToken=uniqid();
$return_page = "Propriete_Document_Category";
?>
<div class="row page_title">
	<div class="col_6-inline icon">
		<i class="fas fa-address-card"></i> Document Catégorie
	</div>
	<div class="col_6-inline actions <?= strtolower($return_page) ?>">
		<button class="btn btn-green save_edit" value="<?= $return_page ?>"><i class="fas fa-save"></i></button>
		<button class="btn btn-default close" value="<?= $return_page ?>"><i class="fas fa-times"></i></button>
	</div>
</div>
<hr>

<div class="panel">
	<div class="panel-header">
	Document Catégorie
	</div>
	<div class="panel-content">

		<div class="menu_form">

			<h3 style="margin-left: 6px">Document Catégorie</h3>
			<input type="hidden" value="<?= $data["id"]  ?>" id="id">
			
			<div class="row" style="margin-bottom: 20px">
				<div class="col_12-inline">
					<input type="text" placeholder="Document catégorie" id="document_category" value="<?= $data["document_category"] ?>">
				</div>		
				
			</div>	
			
		</div>		


	</div>


</div>

<div class="debug_client"></div>

