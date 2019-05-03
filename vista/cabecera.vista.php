<header>
        <div class="row navbar-0">
          <div class="col-sm-6">
            <img src="../imagenes/cayalti-izq.jpg" class="img-logo-izq">
          </div>
          <div class="col-sm-6 navbar-link-right">
            <a href="#"><?php echo $nombreUsuario.' ('.$perfil.')'; ?> </a>
            <a href="#" onclick="Util.cerrarSesion();">Cerrar Sesión</a>
          </div>
        </div>
       <div id="navbar" class="navbar-collapse collapse navbar-1">
              <ul class="nav navbar-nav">
                <li>
                  <a href="principal.vista.php">Inicio</a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Gestión <span class="caret"></span></a>
                  <ul class="dropdown-menu dropdown-menu-cayalti">
                    <li><a href="regiones.vista.php">Regiones </a></li>
                    <li><a href="gestion.campos.vista.php">Campos </a></li>
                    <li><a href="gestion.parcelas.vista.php">Parcela</a></li>
                    <li class="divider"></li>
                    <li><a href="colaboradores.vista.php">Colaboradores</a></li>
                    <li><a href="perfiles.vista.php">Perfiles</a></li>
                  </ul>
                </li>  
                 <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Consultas <span class="caret"></span></a>
                  <ul class="dropdown-menu dropdown-menu-cayalti">
                    <li><a href="registros.vista.php">Ver Registros</a></li>
                    <li><a href="reporteador.demo.vista.php">Reporteador</a></li>
                    <li><a href="sql.ejecutor.vista.php">Consultas SQL</a></li>
                  </ul>
                </li>  
              </ul>
        </div>
</header>