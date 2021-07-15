<?php
require_once('Helpers/Modal.php');
require_once('Helpers/View.php');
require_once('Proprietaire.php');


class Propriete extends Modal{

	private $tableName = __CLASS__;
	
// construct
	public function __construct(){
		try{
			parent::__construct();
			$this->setTableName(strtolower($this->tableName));
/*
			$data = $this->find('', ['conditions'=>['YEAR(created)='=>2021]], 'propriete_location');

			foreach($data as $d){
				$contrat = $this->find('', ['conditions'=>['id='=>$d["UID"]]], 'contrat');
				if(count($contrat)){
					$this->save([
						'id'	=>	$d['id'],
						'UID'	=>	$contrat[0]['UID']
					], 'propriete_location');
				}
			}
*/
		}catch(Exception $e){
			die($e->getMessage());
		}
	}	
	
	public function getColumns($style = null){
		
		$style = (is_null($style))? strtolower($this->tableName): $style;
		
		$columns = array();
		$l = new ListView();
		foreach($l->getDefaultStyle($style, $columns)["data"] as $k=>$v){
			array_push($columns, array("column" => $v["column"], "label" => $v["label"], "style"=>$v["style"], "display"=>$v["display"], "format"=>$v["format"]) );
		}
		array_push($columns, array("column" => "actions", "label" => "", "style"=>"min-width:105px; width:105px", "display"=>1) );
		return $columns;
		
	}
	
	public function Table($params = []){

		$remove_sort = array("actions","nbr","nbr_nuite","total");
		$column_style = (isset($params['column_style']))? $params['column_style']: strtolower($this->tableName);
		
		$filters = (isset($params["filters"]))? $params["filters"]: [];
		
		$l = new ListView();
		$defaultStyleName = $l->getDefaultStyleName($column_style);
		$columns = $this->getColumns($column_style);
		
		$table = '
			<div class="table-container">
				<div class="d-flex space-between" style="padding:0 10px 10px 10px">
					<div style="font-size:16px; font-weight:bold">{{counter}}</div>
					<div class="text-green" style="font-size:16px; font-weight:bold">{{total}}</div>
				</div>
				<table  id="tablepropriete">	
					<thead>	
						<tr>
							{{ths}}
						</tr>
						
					</thead>
					<tbody>
						{{trs}}
					</tbody>
				</table>
			</div>
		
		';
		
		/***********
			Columns
		***********/
		$ths = '';
		$trs_counter = 1;
		
		foreach($columns as $column){
			$is_sort = ( in_array($column["column"], $remove_sort) )? "" : "sort_by";
			$style = ""; 
			$is_display = ( isset($column["display"]) )? ($column["display"]? "" : "hide") : "";
			
			if($column['column'] === "actions"){
				$ths .= "<th class='". $is_display . "'>";
				$ths .= "	<button data-default='".$defaultStyleName."' value='".$column_style."' class='show_list_options'>";
				$ths .= "		<i class='fas fa-ellipsis-h'></i></button>";
				$ths .= "	</button>";
				$ths .=	"</th>";
			}else{
				$trs_counter += $is_display === "hide"? 0:1;
				$ths .= "<th class='".$is_sort." ". $is_display . "' data-sort='" . $column['column'] . "' data-sort_type='desc'>";
				$ths .=  "	<div class='d-flex'>";
				$ths .=  		$column['label'];
				$ths .= "		<i class='pl-5 fas fa-sort'></i> ";
				$ths .=  "	</div>";
				$ths .=	"</th>";
			}

		}
		
		/***********
			Conditions
		***********/
		
		$request = [];
		$sql = '';
		if(isset($params['request'])){
			if( $params['request'] !== "" ){
				if( isset($params['tags']) ){
					if( count( $params['tags'] ) > 0 ){
						foreach( $params['tags'] as $k=>$v ){
							$request[ 'LOWER(CONVERT(' . $v. ' USING latin1)) like '] = '%' . strtolower( $params['request'] ) . '%';
							$item = 'LOWER(CONVERT(' . $v. ' USING latin1)) like %' . strtolower( $params['request'] ) . '%';
							$sql .= $sql===''? $item.'<br>': ' AND '.$item.'<br>';
							
						}
					}
				}
			}
		}
		
		$year = "";
		if( count($filters) > 0 ){
			foreach($filters as $k=>$v){
				if($v["value"] !== "-1"){
					if( $v["id"] === "Categorie" ){
						$request['id_propriete_category = '] = $v["value"];
						$item = 'id_propriete_category = ' . $v["value"];						
					}

					if( $v["id"] === "Type" ){
						$request['id_propriete_type = '] = $v["value"];
						$item = 'id_propriete_type = ' . $v["value"];						
					}
					
					if( $v["id"] === "Status" ){
						$request['id_propriete_status = '] = $v["value"];
						$item = 'id_propriete_status = ' . $v["value"];						
					}
					
					if( $v["id"] === "Complexe" ){
						$request['id_complexe = '] = $v["value"];
						$item = 'id_complexe = ' . $v["value"];						
					}
					
					if( $v["id"] === "Vente" ){
						$request['is_for_sell = '] = $v["value"];
						$item = 'is_for_sell = ' . $v["value"];						
					}
					
					if( $v["id"] === "Location" ){
						$request['is_for_location = '] = $v["value"];
						$item = 'is_for_location = ' . $v["value"];						
					}
					
					if( $v["id"] === "Mois" ){
						$request['MONTH(created) = '] = $v["value"];
						$item = 'MONTH(created) = ' . $v["value"];						
					}

					if( $v["id"] === "Années" ){
						$year = $v["value"];
						$item = 'YEAR(created) = ' . $v["value"];	
					}

					$sql .= $sql===''? $item.'<br>': ' AND '.$item.'<br>';					
				}
				
			}

		}
		
		/***********
			Body
		***********/
		$use = (isset($params['use']))? strtolower($params['use']): strtolower($this->tableName);
		
		$conditions = [];
		
		if( count($request) === 1 ){
			$conditions['conditions'] = $request;
		}elseif( count($request) > 1 ){
			$conditions['conditions AND'] = $request;
		}
		
		$conditions['order'] = isset($params['sort'])? $params['sort']: 'created desc';
		
		$pp = isset( $params['pp'] ) ? $params['pp']: 20;
		
		$current = isset( $params['current'] ) ? $params['current']: 0;
		
		//$conditions['limit'] = [$current,$pp];
		
		$data = $this->find('', $conditions, $use);
		
		// Counter
		$counter = count($data);
		
		$trs = '';

		foreach($data as $k=>$v){
			$contrat = [];
			$status = "";
			$background = "";
			$nbr_nuite = 0;
			$total = 0;
			$req = "";
			

			if($year !== ""){
				$req = "select distinct id_propriete from propriete_location where year(created) = " . $year . " and id_propriete group by id_propriete";
				$contrat = $this->find('', array("conditions AND"=>array("YEAR(created)="=>$year, "id_propriete="=>$v["id"], "status="=>1)), "v_propriete_proprietaire_location");

				$status = $this->Get_Status_Of_Propriete(['year'=>$year, 'id_propriete'=>$v['id']]);
				$background = $status? $status["hex_string"]: "";
				$status = $status? $status["propriete_status"]: "";			
				
			}else{
				$contrat = $this->find('', array("conditions AND"=>array("id_propriete="=>$v["id"], "status="=>1)), "v_propriete_proprietaire_location");
				$status = "";
				$background = "";
			}

			foreach($contrat as $kk=>$vv){
				$total +=  ($vv["id_propriete_location_type"] === "1")? ($vv["nbr_nuite"] * $vv["montant"]): $vv["montant"];
				$nbr_nuite += $vv["nbr_nuite"];
			}

			$hide = $nbr_nuite === 0? ($year === ""? "": "hide"): "";

			$trs .= '<tr class="'.$hide.'" style="background-color:'.$background.'" data-page="'.$use.'">';
			foreach($columns as $key=>$value){
				
				$style = (!$columns[$key]["display"])? "display:none": $columns[$key]["style"] ;
				$is_display = (!$columns[$key]["display"])? "hide": "" ;

				if(isset($v[ $columns[$key]["column"] ])){
					
					if($columns[$key]["column"] == "propriete_status"){
						$trs .=   "<td style='width:55px; text-align: center'>".$status."</td>";	
					
					}elseif(isset($columns[$key]["format"])){
						if($columns[$key]["format"] === "money"){
							$trs .= "<td class='".$is_display."' style='".$style."'>" . $this->format($v[ $columns[$key]["column"] ]) . "</td>";
						}else if($columns[$key]["format"] === "on_off"){
							$trs .= "<td class='".$is_display."' style='".$style."'><div class='label label-red'>Désactive</div></td>";
						}else if($columns[$key]["format"] === "color"){
							$trs .= "<td class='".$is_display."' style='".$style."'> <span style='padding:10px 15px; background-color:".$v[ $columns[$key]["column"] ]."'>".$v[ $columns[$key]["column"] ] . "</span></td>";
						}else if($columns[$key]["format"] === "date"){
							$date = explode(" ", $v[ $columns[$key]["column"] ]);
							if(count($date)>1){
								$_date = "<div style='min-width:105px'><i class='fas fa-calendar-alt'></i> ".$date[0]."</div><div style='min-width:105px'><i class='far fa-clock'></i> ".$date[1]."</div>";
							}else{
								$_date = "<div><i class='fas fa-calendar-alt'></i> ".$date[0]."</div>";
							}
							$trs .= "<td class='".$is_display."' style='".$style.";'>".$_date."</td>";

						}else{
							$trs .= "<td class='".$is_display."' style='".$style."'>".$v[ $columns[$key]["column"] ]. "</td>";
						}
					}else{
						$trs .= "<td class='".$is_display."' style='".$style."'>".$v[ $columns[$key]["column"] ]."</td>";
					}											
				}else{
					if($columns[$key]["column"] == "actions"){
						$trs .=   "<td style='width:55px; text-align: center'><button data-controler='". $this->tableName ."' class='update' value='".$v["id"]."'><i class='fas fa-ellipsis-v'></i></button></td>";	
					
					}elseif($columns[$key]["column"] == "total"){
						$trs .= "<td class='".$is_display."' style='".$style."'>" . $this->format($total) . "</td>";
					}else{
						
						if($columns[$key]["format"] === "money"){
							$trs .= "<td class='".$is_display."' style='".$style."'>" . $this->format($v[ $columns[$key]["column"] ]) . "</td>";
						}elseif($columns[$key]["column"] == "nbr_nuite"){
							$trs .= "<td style='".$style."'>
										<button class='show_propriete_proprietaire' data-id='".$v['id']."'>" . $nbr_nuite . "</button>
										<button class='show_propriete_proprietaire_locations' data-id='".$v['id']."'>Nuites</button>
									</td>";
						}else if($columns[$key]["format"] === "on_off"){
							$trs .= "<td class='".$is_display."' style='".$style."'><div class='label label-red'>Désactive</div></td>";
						}else if($columns[$key]["format"] === "color"){
							$trs .= "<td class='".$is_display."' style='".$style."'> <span style='padding:10px 15px; background-color:".$v[ $columns[$key]["column"] ]."'>".$v[ $columns[$key]["column"] ] . "</span></td>";
						}else if($columns[$key]["format"] === "date"){
							$date = explode(" ", $v[ $columns[$key]["column"] ]);
							if(count($date)>1){
								$_date = "<div style='min-width:105px'><i class='fas fa-calendar-alt'></i> ".$date[0]."</div><div style='min-width:105px'><i class='far fa-clock'></i> ".$date[1]."</div>";
							}else{
								$_date = "<div><i class='fas fa-calendar-alt'></i> ".$date[0]."</div>";
							}
							
							$trs .= "<td class='".$is_display."' style='".$style.";'>".$_date."</td>";
						}else{
							$trs .= "<td class='".$is_display."' style='".$style."'>" . "NaN" . "</td>";
						}						
					}

				}


			}
			
			$trs .= '</tr>';
			
		}
		
		if(count($data) === 0)
			$trs = '<tr><td colspan="'.$trs_counter.'">No Data to Display!</td></tr>';
		
		$counter = $counter . " Operations";
		return str_replace(["{{ths}}", "{{trs}}", "{{sql}}", "{{counter}}"], [$ths, $trs, $sql, $counter], $table);
		
	}
	
	public function Create($params = []){
		$push = [];
		$push['complexe'] =	$this->find('', ['order' => 'name desc'], 'complexe');
		$push['propriete_category'] =	$this->find('', ['order' => 'propriete_category desc'], 'propriete_category');
		$push['propriete_type'] =	$this->find('', ['order' => 'propriete_type desc'], 'propriete_type');
		$push['propriete_status'] =	$this->find('', ['order' => 'propriete_status desc'], 'propriete_status');
		$push['Obj']	=	new Propriete;
		
		$view = new View("propriete.create");
		
		return $view->render($push);
	}
	
	public function Update($params){
		
		$push = [];
		$push['complexe'] =	$this->find('', ['order' => 'name desc'], 'complexe');
		$push['propriete_category'] =	$this->find('', ['order' => 'propriete_category desc'], 'propriete_category');
		$push['propriete_type'] =	$this->find('', ['order' => 'propriete_type desc'], 'propriete_type');
		$push['propriete_status'] =	$this->find('', ['order' => 'propriete_status desc'], 'propriete_status');
		$push['depenses'] = $this->find('', [ 'conditions' => ['id_propriete=' => $params['id'] ] ], 'depense');
		$push['notess'] = $this->find('', [ 'conditions AND' => ['module='=>'propriete', 'id_module=' => $params['id'] ], 'order'=>'created DESC' ], 'notes');
		
		$push['Obj']	=	new Propriete;
		
		$propriete = $this->find('', [ 'conditions'=>[ 'id='=>$params['id'] ] ], '');		
		if( count($propriete) > 0 ){
			$proprietaire = $this->find('', ['conditions' => ['id=' => $propriete[0]['id_proprietaire'] ] ], 'proprietaire');
			$push['propriete'] = $propriete[0];
			if(count($proprietaire) > 0)
				$push['proprietaire'] = $proprietaire[0];
		}
		
		
		$view = new View("propriete.create");
		
		return $view->render($push);
	}
	
	public function Store($params){
				
		$created = date('Y-m-d H:i:s');
		$created_by	=	$_SESSION[ $this->config->get()['GENERAL']['ENVIRENMENT'] ]['USER']['id'];
		
		$proprietaire = new Proprietaire;
		$id_proprietaire = 0;
		
		$data = [
			'UID'						=>	addslashes($params['columns']['UID']),
			'created'					=>	$created,
			'created_by'				=>	$created_by,
			'updated'					=>	$created,
			'code'						=>	$params['columns']['propriete_code'],
			'id_complexe'				=>	$params['columns']['propriete_complexe'],
			'zone_number'				=>	$params['columns']['zone_number'],
			'bloc_number'				=>	$params['columns']['bloc_number'],
			'appartement_number'		=>	$params['columns']['appartement_number']===''? 0: $params['columns']['appartement_number'],
			'surface'					=>	$params['columns']['surface']===''? 0: $params['columns']['surface'],
			'nbr_chambre'				=>	$params['columns']['nbr_chambre']===''? 0: $params['columns']['nbr_chambre'],
			'etage_number'				=>	$params['columns']['etage_number'],
			'maximum_person'			=>	$params['columns']['maximum_person']===''? 0: $params['columns']['maximum_person'],
			'id_propriete_category'		=>	$params['columns']['propriete_category'],
			'id_propriete_type'			=>	$params['columns']['propriete_type'],
			'id_propriete_status'		=>	$params['columns']['propriete_status'],
			'notes'						=>	addslashes($params['columns']['notes']),
			'is_for_sell'				=>	$params['columns']['is_for_sell'],
			'is_for_location'			=>	$params['columns']['is_for_location'],
		];
		
		$proprietaire_data = [ 'columns' => [
								'name'						=>	addslashes($params['columns']['proprietaire_name']),
								'cin'						=>	'',
								'passport'					=>	'',
								'phone_1'					=>	addslashes($params['columns']['proprietaire_telephone']),
								'phone_2'					=>	addslashes($params['columns']['proprietaire_telephone_2']),
								'adresse'					=>	'',
								'ville'						=>	'',
								'email'						=>	addslashes($params['columns']['proprietaire_email']),
								'agence_1'					=>	addslashes($params['columns']['proprietaire_agence']),
								'agence_2'					=>	'',
								'rib_1'						=>	addslashes($params['columns']['proprietaire_rib']),
								'rib_2'						=>	'',
								'notes'						=>	'',
								'status'					=>	1				
							]
						];		
		if($params["columns"]["id_proprietaire"] !== ""){
			$proprietaire->setID($params['columns']['id_proprietaire']);
			$prop = $proprietaire->read();
			if(count($prop) > 0){
				$proprietaire_data["columns"]["id"] = $params['columns']['id_proprietaire'];
				$proprietaire_data["columns"]["cin"] = $prop[0]["cin"];
				$proprietaire_data["columns"]["passport"] = $prop[0]["passport"];
				$proprietaire_data["columns"]["ville"] = $prop[0]["ville"];
				$proprietaire_data["columns"]["adresse"] = $prop[0]["adresse"];
				$proprietaire_data["columns"]["agence_2"] = $prop[0]["agence_2"];
				$proprietaire_data["columns"]["rib_2"] = $prop[0]["rib_2"];
				$proprietaire_data["columns"]["notes"] = $prop[0]["notes"];
				
				if($prop[0]["UID"] === "0" || $prop[0]["UID"] === ""){
					$proprietaire_data["columns"]["UID"] = md5( uniqid('auth', true) );
				}else{
					$proprietaire_data["columns"]["UID"] = $prop[0]["UID"];
				}
				$proprietaire->Store($proprietaire_data);	
				$id_proprietaire = $params['columns']['id_proprietaire'];
			}
		}else{
			$proprietaire_data["columns"]["UID"] = md5( uniqid('auth', true) );
			$proprietaire->Store($proprietaire_data);
			$id_proprietaire = $proprietaire->getLastID();
		}
		
		$data["id_proprietaire"] = $id_proprietaire;
		
		if( isset($params['columns']["id"]) ){
			unset($data["created"], $data["created_by"]);
			$data["id"] = $params['columns']["id"];		
		}
		
		if($this->save($data)){
			if(isset($data["id"])){
				$msg = "Code: " . $data["code"];
				$this->saveActivity("fr", $created_by, ['Propriete', 0], $data["id"], $msg);

				$status = $this->Get_Status_Of_Propriete(['year'=>date('Y'), 'id_propriete'=>$data['id']]);
				if($status){
					if( $status['id_propriete_status'] != $data['id_propriete_status'] ){
						$data_ = [
							'created'				=>	$created,
							'created_by'			=>	$created_by,
							'id_propriete_status'	=>	$data['id_propriete_status'],
							'id_propriete'			=>	$data['id']
						];	
						$this->save($data_, 'status_of_propriete');						
					}
				}else{
					$data_ = [
						'created'				=>	$created,
						'created_by'			=>	$created_by,
						'id_propriete_status'	=>	$data['id_propriete_status'],
						'id_propriete'			=>	$data['id']
					];	
					$this->save($data_, 'status_of_propriete');
				}
			}else{
				$msg = "Code: " . $data["code"];
				$this->saveActivity("fr", $created_by, ['Propriete', 1], $this->getLastID(), $msg);
				$data_ = [
					'created'				=>	$data['created'],
					'created_by'			=>	$data['created_by'],
					'id_propriete_status'	=>	$data['id_propriete_status'],
					'id_propriete'			=>	$data['id']
				];	
				$this->save($data_, 'status_of_propriete');
			}
			return 1;
			
		}else{
			return $this->err;
		}		
		
	}
	
	public function Remove($params){
		if(isset($params["id"])){
			
			$data = $this->find('', ['conditions' => [ 'id=' => $params['id'] ] ], '');
			if(count($data) === 1){
				
				if( count($this->find('', ['conditions' => ['id_propriete='=>$params['id']] ], 'depense') ) === 0 ){
					if( count($this->find('', ['conditions' => ['id_propriete='=>$params['id']] ], 'propriete_location') ) === 0 ){
						if( count($this->find('', ['conditions' => ['id_propriete='=>$params['id']] ], 'propriete_proprietaire_location') ) === 0 ){	
							if( count($this->find('', ['conditions AND' => ['module='=>'propriete', 'id_module='=>$params['id']] ], 'notes') ) === 0 ){

								$data = $data[0];
								$created_by	=	$_SESSION[ $this->config->get()['GENERAL']['ENVIRENMENT'] ]['USER']['id'];
								$msg = "Montant: " . $data["code"];
								$this->delete($params["id"]);

								$this->saveActivity("fr", $created_by, ['Propriete', -1], $data["id"], $msg);

								return 1;					
							}else{
								return 0;
							}					
						}else{
							return 0;
						}					
					}else{
						return 0;
					}
				}else{
					return 0;
				}
				
				

			}else{
				return 0;
			}

		}else{
			return 0;
		
		}
	}
	
	public function IsHasProprietaireContrat($params){
		
		$returned = [];
		
		if(!empty($params)){
			
			$date_debut = $params["date_debut"];
			$date_fin = $params["date_fin"];
			$id_propriete = $params["id_propriete"];
			
			$request = "select * from propriete_proprietaire_location join v_propriete on v_propriete.id=propriete_proprietaire_location.id_propriete where (de<=CAST('".$date_debut."' AS DATE) and a>=CAST('".$date_fin."' AS DATE)) AND id_propriete=".$id_propriete;
			if (count($this->execute($request)) > 0) return 1; else return 0;
		}
		
		return 0;
		
	}
	
	public function getProprieteDisponible($params){
		
		$returned = [];
		
		if(!empty($params)){
			
			$date_debut = $params["date_debut"];
			$date_fin = $params["date_fin"];
			$code = ($params["code"] !== "")? " AND LOWER(code) like '%".$params["code"]."%'":"";

			$request = "select * from propriete_proprietaire_location join v_propriete on v_propriete.id=propriete_proprietaire_location.id_propriete where (de<=CAST('".$date_debut."' AS DATE) and a>=CAST('".$date_fin."' AS DATE))".$code." ORDER BY v_propriete.name,v_propriete.code ASC";

			foreach($this->execute($request) as $k=>$v){	
				$table = [
					"id_propriete"	=>	$v["id_propriete"],
					"code"			=>	$v["code"],
					"complexe"		=>	$v["name"],
					"proprietaire"	=>	$v["proprietaire"]
				];
				
				array_push($returned, $table);
							
			}
		}
		
		return $returned;
		
	}
	
	public function IsDisponibleOnThisPeriode($params){
		$date_debut = $params["date_debut"];
		$date_fin = $params["date_fin"];
		$id_propriete = $params["id_propriete"];

		$request = "SELECT * FROM propriete_location where id_propriete=".$id_propriete;
		$request .= " AND ( ";
		$request .= " 		( date_debut <= CAST('".$date_debut."' AS DATE) AND date_fin > CAST('".$date_debut."' AS DATE) )";
		$request .= " 		 OR ";
		$request .= " 		( date_debut < CAST('".$date_fin."' AS DATE) AND date_fin >= CAST('".$date_fin."' AS DATE) ) ";
		$request .= " )";
		
		if( count( $this->execute($request) ) > 0 ){
			return 0;
		}else{
			return 1;
		}
	}
	
	public function notes(){
		$notes = $this->find('', array(), '');
		foreach($notes as $k=>$v){
			if ($v["notes"] !== ""){
				$is = preg_split('/\r\n|\r|\n/', $v["notes"]);
				for($i=0; $i<count($is); $i++){			
					$data = array(
						"module"		=>	"propriete",
						"id_module"		=>	$v["id"],
						"created_by"	=>	$v["created_by"],
						"notes"			=>	addslashes($is[$i])
					
					);
					
					$this->save($data, "notes");
				}
			}
			
		}
		echo "finshed";

	}
	
	public function Search($params=[]){
		$template = '
			<table class="table">
				<thead>
					<tr>
						<th style="width:120px">CODE</th>
						<th>COMPLEXE</th>
						<th style="width:69px; max-width:69px"></th>
					</tr>
				</thead>
				<tbody>
					{{trs}}
				</tbody>
			</table>
				';	
		$trs = '		<tr>
							<td colspan="3">
								<div class="text-center p-20 white">
									<div class="m-20 green-text" style="font-size:62px">
										<i class="fab fa-keycdn"></i>
									</div>
									<div class="mt-10 mb-15">
										Aucune Appartement trouvé
									</div>
								</div>
							</td>
						<tr>
				';
		
		if(!empty($params)){
			$conditions = $params["conditions"];
			$use = isset($params["use"])? $params["use"]: '';
			//$conditions = count($conditions)>1? ['conditions AND'=>$conditions]: ['conditions'=>$conditions];
			$proprietes = $this->find('', $conditions, $use);
			

			
			$trs = '';
			foreach($proprietes as $k=>$v){
				$trs .= "		<tr>
									<td style='background-color:rgba(0,255,0,0.1); font-weight:bold; font-size:12px'>".$v['code']."</td>
									<td>".$v['name']."</td>
									
									<td>
										<button data-numero='".$v['appartement_number']."' data-id='".$v['id']."' data-zone='".$v['zone_number']."' data-bloc='".$v['bloc_number']."' data-complexe='".$v['name']."' data-code='".$v['code']."' style='width:100%;padding:5px' class='btn btn-green select_this_propriete'>
											<i class='fas fa-check'></i> 
										</button>
									</td>
								<tr>";
			}
		}
		
		return str_replace("{{trs}}", $trs, $template);
	}
	
	public function FindBy($params){
		
		$code = addslashes( strtolower($params['request']) );
		$data = $this->find('', ['conditions'=>['lower(code)='=>$code] ], '');
		return count( $data ) === 1? $data[0]: 0;
		
	}
	
	public function GetFiles($params){
		
		$statics = $_SESSION["STATICS"];
		
		$folder = $_SESSION["UPLOAD_FOLDER"].$params["folder"].DIRECTORY_SEPARATOR.$params["UID"].DIRECTORY_SEPARATOR;
		
		$dS = DIRECTORY_SEPARATOR;
		
		$icons = [
			'doc'	=>	$statics."public/images/icon_word.png",
			'docx'	=>	$statics."public/images/icon_word.png",
			'pdf'	=>	$statics."public/images/icon_pdf.png",
			'jpg'	=>	$statics."public/images/images.png",
			'jpeg'	=>	$statics."public/images/images.png",
			'png'	=>	$statics."public/images/images.png",
			'gif'	=>	$statics."public/images/images.png",
			'bmp'	=>	$statics."public/images/images.png"
		];
		
		$default_src = $statics."/public/images/images.png";
		
		$array_src = [];
						
		if(file_exists($folder)){

			foreach(scandir($folder) as $k=>$v){
				if($v <> "." and $v <> ".." and strpos($v, '.') !== false){
					
					$ext = explode(".",$v);
					$file_name = $ext[1];
					
					if( isset( $icons[$ext[1]] ) ){
						$file = [
							'file_name'	=>	$v,
							'file_icon'	=>	$icons[$ext[1]],
							'file_src'	=>	$statics.$params["folder"]."/".$params["UID"]."/".$v,
							
							'file_link'	=>	$folder.$v
						];
						array_push( $array_src, $file ) ;
					}
				}
			}	
		}
		
		return $array_src;
	}
	
	public function GetFilesAsList($params){
		//sleep(3);
		$images = $this->GetFiles($params);
		
		$template = '
			
			<div class="list-image">
				<ul>
					{{li}}
				</ul>
			</div>
		
		';
		$lis = '';
		
		$empty = '
			<li>
				<div style="width:100%; height:150px">
					<button style="width:100%; height:100%; font-size:96px; color:grey" class="upload_btn" data-target="upload"><i class="fas fa-folder-plus"></i></button>
				</div>
			</li>
		
		';
		
		foreach($images as $image){

			$lis .= '
					<li>
						<div class="image">
							<img class="download_file" data-link="' . $image["file_src"] . '" src="'.$image["file_icon"].'">
						</div>
						<div class="info" style="flex:1; text-align:left">
							<div class="name">' . $image["file_name"] . '</div>
						</div>
						
						<div class="image_actions">
							<button class="red remove-file" data-uid="' . $params["UID"] . '" data-folder="' . $params["folder"] . '" data-controler="Propriete" data-function="DeleteFile" data-filename="' . $image["file_name"] . '"><i class="far fa-trash-alt"></i></button>
						</div>
					</li>
			
			';
		}
		$lis = $lis===''? $empty: $lis;
		return str_replace(["{{li}}"], [$lis], $template);
	}
	
	public function DeleteFile($params){
		$created_by = $_SESSION[ $this->config->get()['GENERAL']['ENVIRENMENT'] ]['USER']['id'];
		
		$this->saveActivity("fr",$created_by,array("Propriete","3"),0,"Fichier : " . $params["file_name"]);
		$folder = $_SESSION["UPLOAD_FOLDER"].$params["folder"].DIRECTORY_SEPARATOR.$params["UID"].DIRECTORY_SEPARATOR.$params["file_name"];
		if(file_exists($folder)){
			return unlink($folder)? 1:0;
		}else{
			return 0;
		}

	}
	
	public function ShortTable_($params = []){
		$template = '
			
			<div class="short_table">
				<div class="search_bar d-flex space-between">
					<input type="text" class="request" data-controler="Propriete" data-id="id_propriete">
					<button><i class="fas fa-check"></i></button>
				</div>
				
				<div class="result">
					{{items}}
				</div>
			</div>
		
		';
		$items = '';
		if( isset($params['request']) ){
			$prop = $this->find('', ['conditions'=>['code like '=>'%'.$params['request'].'%'], 'order'=>'code DESC'], 'v_propriete');
		}else{
			$prop = $this->find('', ['order'=>'code DESC'], 'v_propriete');
		}
		
		foreach( $prop as $k=>$v){
			$active = isset($params['id_table'])? ($params['id_table'] === $v["id"]? "active": ""): "";
			$items .= '
					<div class="item d-flex space-between '.$active.'">
						<div class="d-flex space-between" style="font-size: 10px">
							<div class="pr-10 pt-10" style="font-weight:bold"> '.$v["code"].' </div>
							<div class="pt-10"> '.$v["proprietaire"].'</div>
						 </div>
						<div> <button class="transparent check_this_propriete disabled"><i class="fas fa-sync-alt"></i></button></div>
					</div>
			';
			
		}
		
		return str_replace("{{items}}", $items, $template);
		
	}
	
	public function ShortTableBy($params = []){

		$items = '';
		if( isset($params['request']) ){
			$prop = $this->find('', ['conditions'=>['code like '=>'%'.$params['request'].'%'], 'order'=>'code DESC'], 'v_propriete');
		}else{
			$prop = $this->find('', ['order'=>'code DESC'], 'v_propriete');
		}
		
		
		foreach( $prop as $k=>$v){
			$active = isset($params['id_table'])? $params['id_table'] === $v["id"]? "active": "": "";
			$items .= '
					<div class="item d-flex space-between '.$active.'">
						<div class="d-flex space-between" style="font-size: 10px">
							<div class="pr-10 pt-10" style="font-weight:bold"> '.$v["code"].' </div>
							<div class="pt-10"> '.$v["proprietaire"].' </div>
						 
						 </div>
						<div> <button class="green">Select </button></div>
					</div>
			';
			
		}
		
		return $items;
		
	}
	
	public function ShortTable($params){
				
		$template = '
			
			<div class="short_table">
				<div class="search_bar" style="position:relative">
					<input type="text" class="request_2" data-controler="Propriete" data-id="id_propriete">
					<div class="result_counter hide" style="position: absolute; top:20px; right:20px; color:green; font-size:10px">0</div>
				</div>
				
				<div class="result">
					{{items}}
				</div>
			</div>
		
		';
		$items = '';
		$empty = '
						<div class="d-flex text-left">
								<div class="info info-success">
									<div class="info-message"> 
									Aucun Appartement n\'est trouvé pour cette periode
									</div>
								</div>				
						</div>
		';
		
		foreach( $this->getProprieteDisponible($params) as $k=>$v){
			
			$isDisponible = $this->IsDisponibleOnThisPeriode([
				"date_debut"	=>	$params["date_debut"],
				"date_fin"		=>	$params["date_fin"],
				"id_propriete"	=>	$v["id_propriete"]
			]);
			
			$btn = '
				<button data-id_propriete="'.$v["id_propriete"].'" data-date_debut="'.$params["date_debut"].'" data-date_fin="'.$params["date_fin"].'"  class="transparent add_this_propriete_to_contrat">
					<i class="fas fa-check"></i> 
				</button>';
			
			if(!$isDisponible){
				$btn = '
				<button  class="red">
					<i class="fas fa-ban"></i> 
				</button>';
			}
			
			
			$items .= '
					<div class="item d-flex space-between">
						<div class="d-flex space-between" style="font-size: 10px">
							<div class="code pr-10 pt-10" style="font-weight:bold"> '.$v["code"].' </div>
							<div class="prop pt-10"> '.$v["proprietaire"].' </div>
						 </div>
						<div> '.$btn.' </div>
					</div>
			';
			
		}
		
		$items = $items === ''? $empty: $items;
		
		return str_replace("{{items}}", $items, $template);
		
		
	}
	
	public function Get_Status_Of_Propriete($params){
		if(isset($params['year'])){
			$request = "
			SELECT status_of_propriete.*, 
				v_propriete_status.propriete_status, 
				v_propriete_status.all_ligne, 
				v_propriete_status.hex_string 
			FROM status_of_propriete 
			LEFT JOIN v_propriete_status ON v_propriete_status.id=status_of_propriete.id_propriete_status 
			WHERE status_of_propriete.id_propriete=".$params['id_propriete']." AND YEAR(status_of_propriete.created)=" . intval($params['year']) . " 
			ORDER BY status_of_propriete.id DESC";
			
			//echo $request;
			$data = $this->execute($request);
			if(count($data)>0){
				return $data[0];
			}else{
				return false;
			}
		}else{
			return false;
		
		}
	}
	
	
	/*
		Liste Propriete Location envers Proprietaire / Client
	*/

	public function contrat_client_locations($params){

		$request = "
		SELECT 
			p_l.id,
			p_l.date_debut as date_debut,
			p_l.date_fin as date_fin,
			p_l.status as status,
			to_days(p_l.date_fin) - to_days(p_l.date_debut) AS nbr_nuite,
			client.first_name as first_name,
			client.last_name as last_name,
			colors.hex_string as hex
		FROM propriete_location as p_l
		LEFT JOIN contrat_periode ON contrat_periode.id = p_l.id_periode
		LEFT JOIN contrat ON contrat.UID = contrat_periode.UID
		LEFT JOIN client ON client.id = contrat.id_client
		LEFT JOIN colors on colors.color_id = client.id_color
		WHERE p_l.id_propriete = ".$params['id_propriete']."
		ORDER BY p_l.date_debut DESC
		";

		$ppl = $this->find('', ['conditions'=>['id_propriete=' => $params['id_propriete'] ], 'order'=>'de DESC'], 'v_propriete_proprietaire_location');
		$pl = $this->execute($request);
		$view = new View("propriete.location");
		return $view->render([
			'ppl'			=>		$ppl,
			'pl'			=>		$pl,
			'Obj'			=>		$this,
			'id_propriete'	=>		$params['id_propriete']
		]);
	}

	/*
		Search Propriete Location envers Proprietaire
	*/
	public function get_proprietaire_locations_by_propriete($params){
		$ppl = $this->find('', ['conditions'=>['id_propriete=' => $params['id_propriete'] ], 'order'=>'created DESC'], 'v_propriete_proprietaire_location');
		$trs = "";
		foreach($ppl as $p){
			$view = new View("propriete.proprietaire_location.tr");
			$trs .= $view->render([
				'p'		=>		$p,
				'Obj'		=>		$this
			]);
		}
		return $trs;
	}
	
	/*
	Create Propriete Location envers Proprietaire
	*/
	public function create_proprietaire_locations($params){
		$view = new View("propriete.proprietaire_location.create");
		return $view->render([
			'Obj'			=>		$this,
			'id_propriete'	=>		$params['id_propriete']
		]);
	}
	/*
	Update Propriete Location envers Proprietaire
	*/
	public function update_proprietaire_locations($params){
		$view = new View("propriete.proprietaire_location.update");
		$ppl = $this->find('', [ 'conditions'=>[ 'id='=>$params['id'] ] ], 'v_propriete_proprietaire_location');	

		return $view->render([
			'Obj'			=>		$this,
			'ppl'	=>		$ppl[0]
		]);
	}

	/*
	Create Propriete Location envers Client
	*/
	public function create_client_locations($params){
		$clients = [];
		$request = "
						SELECT 
							client.first_name,
							client.last_name,
							client.societe_name,
							client.id as id_client,
							contrat.UID as contrat_UID,
							YEAR(contrat.created) as annee,
							c_p.date_debut as date_debut,
							c_p.date_fin as date_fin,
							c_p.nbr_nuite as nbr_nuite,
							c_p.status as status
						FROM contrat
						LEFT JOIN client ON client.id = contrat.id_client
						LEFT JOIN contrat_periode c_p on c_p.UID=contrat.UID
						WHERE contrat.status=1
						ORDER BY annee DESC,societe_name ASC
						";
		$periodes = $this->execute($request);

		foreach($periodes as $c_p){
			$isDisponible = $this->IsHasProprietaireContrat(
				[
					'id_propriete'	=>	$params['id_propriete'], 
					'date_debut'	=>	$c_p["date_debut"], 
					'date_fin'		=>	$c_p["date_fin"]
				]);
			if($isDisponible){
				$isDisponibleOnThisPeriode = $this->IsDisponibleOnThisPeriode(
					[
						'id_propriete'	=>	$params['id_propriete'], 
						'date_debut'	=>	$c_p["date_debut"], 
						'date_fin'		=>	$c_p["date_fin"]
					]);

				if ( $isDisponibleOnThisPeriode ){
					$c_p["msg"] = "disponible";
					$clients[] = $c_p;
				}
			}
		}

		$view = new View("propriete.client_location.create");
		return $view->render([
			'Obj'			=>		$this,
			'id_propriete'	=>		$params['id_propriete'],
			'clients'		=>		$clients	
		]);
	}

	public function get_periodes_by_client($params){
		$periodes_ = $this->find('', ['conditions AND'=>[ 'UID='=>$params['UID'], 'status='=>1 ] ], 'contrat_periode');
		$periodes = [];

		foreach($periodes_ as $p){
			$isDisponible = $this->IsHasProprietaireContrat(['id_propriete'=>$params['id_propriete'], 'date_debut'=>$p["date_debut"], 'date_fin'=>$p["date_fin"]]);
			$periodes[] = [
				'date_debut'	=>	$p["date_debut"],
				'date_fin'		=>	$p["date_fin"],
				'id_periode'	=>	$p["id"],
				'status'		=>	$isDisponible? "dispo": "non dispo"
			];
		}

		$view = new View("propriete.client_location.periodes");
		return $view->render([
			'Obj'			=>		$this,
			'id_propriete'	=>		$params['id_propriete'],
			'id_client'		=>		$params['id_client'],
			'UID'			=>		$params['UID'],
			'periodes'		=>		$periodes	
		]);
	}

}
$propriete = new Propriete;