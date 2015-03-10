<?php
	$month_name = "Enero";
	$cr_name = "Adam Brakhane";
?>
<?php include('head.php'); ?>
<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
		<h1><?=$month_name;?></h1>
		<p class="lead"><?=$cr_name;?></p>
		</div>
		<div class="col-lg-4 col-md-5 col-sm-6">
			<p>Content<br>Content<br>Content<br>Content<br></p>
		</div>
	</div>
</div>
		
		

<div class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="save_section" style="float:right;margin:10px 10px;"><a href="#" class="btn btn-success btn-xs">guardar sección</a></div>
			<h4 id="">Communidades</h4>
		</div>
		<div class="panel-body">
			
			<div class="bs-component">
				<table class="table table-striped table-hover" id="community_list">
					<thead>
						<tr>
							<th>#</th>
							<th>Comunidad</th>
							<th>Fecha</th>
							<th>Valor de Cloro<br>(mg/L)</th>
							<th>Lugar de Muestra(s) de Cloro</th>
							<th>Problemas</th>
							<th>Detalles</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>1</td>
							<td>Marcala</td>
							<td>	
								<input type="text" class="form-control input-sm" autocomplete="off" placeholder="dia/mes/ano">
							<td>
								<input type="text" class="form-control input-sm" autocomplete="off" placeholder="mg/L">
							</td>
							<td>
								<input type="text" class="form-control input-sm" autocomplete="off" placeholder="tanque 1">
							</td>
							<td>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="junta"> Junta
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="agua"> Agua
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="cloro"> Cloro
								</label>
								<br>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="clorador"> Clorador
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="otro"> Otro (describe)
								</label>
							</td>
							<td>
								<textarea class="form-control"></textarea>
							</td>
						</tr>
						<tr>
							<td>2</td>
							<td>Fatima</td>
							<td>	
								<input type="text" class="form-control input-sm" autocomplete="off" placeholder="dia/mes/ano">
							<td>
								<input type="text" class="form-control input-sm" autocomplete="off" placeholder="mg/L">
							</td>
							<td>
								<input type="text" class="form-control input-sm" autocomplete="off" placeholder="tanque 1">
							</td>
							<td>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="junta"> Junta
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="agua"> Agua
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="cloro"> Cloro
								</label>
								<br>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="clorador"> Clorador
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" id="problems" value="otro"> Otro (describe)
								</label>
							</td>
							<td>
								<textarea class="form-control"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="bs-docs-section">
	<div class="row">
		<div class="panel panel-primary">
			
			<div class="panel-heading">
				<div class="save_section" style="float:right;margin:10px 10px;"><a href="#" class="btn btn-success btn-xs">guardar sección</a></div>
				<h4>Eventos Especiales</h4>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover" id="special_events">
					<thead>
						<tr>
							<th>#</th>
							<th>Comunidad</th>
							<th>Fecha</th>
							<th>Detalles</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>1</td>
							<td>Marcala</td>
							<td>20/01/2015</td>
							<td>algo aqui...</td>
						</tr>
						<tr>
							<td>2</td>
							<td>Fatima</td>
							<td>20/01/2015</td>
							<td>diana visitado</td>
						</tr>
						<tr>
							<td>3</td>
							<td>
								<select class="form-control" id="select">
									<option>Marcala</option>
									<option>Fatima</option>
								</select>
							<td><input type="text" class="form-control input-sm" autocomplete="off" placeholder="dia/mes/ano"></td>
							<td><textarea class="form-control"></textarea></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('foot.php'); ?>