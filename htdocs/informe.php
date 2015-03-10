<?php include('head.php'); ?>
<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
		<h1>Informe Nuevo</h1>
		</div>
		<div class="col-lg-4 col-md-5 col-sm-6">
			<p>Content<br>Content<br>Content<br>Content<br></p>
		</div>
	</div>
</div>
<style>
.cblabel_left {
	width:80%;
}
input[type="radio"], input[type="checkbox"] {
	float:right;
}
</style>
<div class="row">
	<div class="well bs-component">
		<form class="form-horizontal" method="post" action="informe.php">
		  <fieldset>
			<legend>Seleccione Opciones</legend>
			<style>.panel-heading {height:60px;}.panel-body {height:200px;overflow-y:auto}</style>
			<div class="col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading"><h3>Cual Comunidades?</h3></div>
					<div class="panel-body">
						<label class="cblabel_left">Todas las comunidades</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 1</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 2</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 3</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 4</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 5</label><input type="checkbox"><br>
						<label class="cblabel_left">Todas las comunidades</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 1</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 2</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 3</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 4</label><input type="checkbox"><br>
						<label class="cblabel_left">Nombre 5</label><input type="checkbox"><br>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading"><h3>Cual Datos?</h3></div>
					<div class="panel-body">
						<label class="cblabel_left">Todos los datos</label><input type="checkbox"><br>
						<label class="cblabel_left">Valor de Cloro</label><input type="checkbox" ><br>
						<label class="cblabel_left">Problemas de la junta</label><input type="checkbox"><br>
						<label class="cblabel_left">Problemas de agua</label><input type="checkbox"><br>
						<label class="cblabel_left">...</label><input type="checkbox"><br>
						<label class="cblabel_left">...</label><input type="checkbox"><br>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-default">
					<div class="panel-heading"><h3>cual tiempo?</h3></div>
					<div class="panel-body">
						<div class="form-group">
							<label for="date_start" class="col-lg-2 control-label">Desde</label>
							<div class="col-lg-10">
								<input type="text" class="form-control" id="date_start" placeholder="2015/02/10" autocomplete="off" style="cursor: auto; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							</div>
							<label for="date_end" class="col-lg-2 control-label">Hasta</label>
							<div class="col-lg-10">
								<input type="text" class="form-control" id="date_end" placeholder="2015/03/10" autocomplete="off" style="cursor: auto; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QsPDhss3LcOZQAAAU5JREFUOMvdkzFLA0EQhd/bO7iIYmklaCUopLAQA6KNaawt9BeIgnUwLHPJRchfEBR7CyGWgiDY2SlIQBT/gDaCoGDudiy8SLwkBiwz1c7y+GZ25i0wnFEqlSZFZKGdi8iiiOR7aU32QkR2c7ncPcljAARAkgckb8IwrGf1fg/oJ8lRAHkR2VDVmOQ8AKjqY1bMHgCGYXhFchnAg6omJGcBXEZRtNoXYK2dMsaMt1qtD9/3p40x5yS9tHICYF1Vn0mOxXH8Uq/Xb389wff9PQDbQRB0t/QNOiPZ1h4B2MoO0fxnYz8dOOcOVbWhqq8kJzzPa3RAXZIkawCenHMjJN/+GiIqlcoFgKKq3pEMAMwAuCa5VK1W3SAfbAIopum+cy5KzwXn3M5AI6XVYlVt1mq1U8/zTlS1CeC9j2+6o1wuz1lrVzpWXLDWTg3pz/0CQnd2Jos49xUAAAAASUVORK5CYII=); background-attachment: scroll; background-position: 100% 50%; background-repeat: no-repeat;">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
			  <div class="col-lg-4 col-lg-offset-4" style="text-align:center;">
				<button class="btn btn-default">Reajustar</button>
				<button type="submit" class="btn btn-primary">Presentar</button>
			  </div>
			</div>
		  </fieldset>
		</form>
	</div>
</div>

<?php include('foot.php'); ?>