
<div id="sidebar" class="sidebar responsive  ace-save-state">
  <script type="text/javascript">
    try{ace.settings.loadState('sidebar')}catch(e){}
  </script>
  <div class="sidebar-shortcuts" id="sidebar-shortcuts">
    <!-- Menú Principal-->
     <div class="menu-principal-label">  Menú Principal</div>
    <!--
    <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
        <button class="btn btn-small btn-success">
          <i class="icon-signal"></i>
        </button>
        
        <button class="btn btn-small btn-info">
          <i class="icon-pencil"></i>
        </button>

        <button class="btn btn-small btn-warning">
          <i class="icon-group"></i>
        </button>

        <button class="btn btn-small btn-danger">
          <i class="icon-cogs"></i>
        </button>
    </div>
      <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
        <span class="btn btn-success"></span>

        <span class="btn btn-info"></span>

        <span class="btn btn-warning"></span>

        <span class="btn btn-danger"></span>
      </div>
      -->
  </div><!--#sidebar-shortcuts-->
  <ul class="nav nav-list">      
        <?php 

          $paginaActual =  basename($_SERVER["SCRIPT_FILENAME"]); 

          $menu = [
            ["rotulo"=>"Mantenimientos", "icon"=>"fa fa-edit",
                "menu"=>[
                          ["rotulo"=>"Área","href"=>"area.vista.php"],
                          ["rotulo"=>"Cargo","href"=>"cargo.vista.php"],
                          ["rotulo"=>"Personal","href"=>"personal.vista.php"],
                          ["rotulo"=>"Tipo Equipo","href"=>"tipo.equipo.vista.php"],
                          ["rotulo"=>"Tipo Problema","href"=>"tipo.problema.vista.php"],
                          ["rotulo"=>"Equipo Problema","href"=>"equipo.problema.vista.php"],
                          ["rotulo"=>"Gestión AHP","href"=>"gestion.ahp.vista.php"]
                ]
            ],
            ["rotulo"=>"Solicitudes", "icon"=>"fa fa-file-o", 
                "menu"=>[
                     ["rotulo"=>"Registrar Solicitud","href"=>"registrar.solicitud.vista.php"],
                     ["rotulo"=>"Lista de Solicitudes","href"=>"listar.solicitudes.vista.php"]
                ]
            ],
            ["rotulo"=>"Incidencias", "icon"=>"fa fa-book", "href"=>"incidencia.vista.php"],
            ["rotulo"=>"Gestión-Cambio", "icon"=>"fa fa-bolt", "href"=>"gestion.cambio.vista.php"],
            ["rotulo"=>"Problemas", "icon"=>"fa fa-cogs", "href"=>"problema.vista.php"]
          ];

          $html = "";

          for ($i=0; $i < count($menu); $i++) { 
            $objMenu = $menu[$i];
            $thisHtml ="";
              if (isset($objMenu["menu"])){
                /*Es un submenuter*/
                  $open = false;
                  $thisHtml .= ' <a href="#" class="dropdown-toggle">
                                <i class="menu-icon '.$objMenu["icon"].'"></i>
                                <span class="menu-text">
                                  '.$objMenu["rotulo"].'
                                </span>

                                <b class="arrow fa fa-angle-down"></b>
                              </a>

                              <b class="arrow"></b>';

                  $thisHtml .= '  <ul class="submenu">';

                  for ($j=0; $j < count($objMenu["menu"]) ; $j++) { 
                    $objSubMenu = $objMenu["menu"][$j];
                    $active = $objSubMenu["href"] == $paginaActual;
                    if ($active){ $open = true;}

                    $thisHtml .= ' <li class="'.($active ? "active" : "").'">
                                    <a href="'.$objSubMenu["href"].'">
                                      <i class="menu-icon fa fa-caret-right"></i> '.$objSubMenu["rotulo"].'                                    
                                    </a>
                                    <b class="arrow"></b>
                                </li>';
                  }

                  $thisHtml .= '  </ul>
                            </li>';

                  $html .= ('<li class="'.($open ? "open" : "").'">'.$thisHtml);

                } else {

                  $active = $objMenu["href"] == $paginaActual;
                  $html .= '<li class="'.($active ? "active" : "").'">
                              <a href="'.$objMenu["href"].'">
                                <i class="menu-icon '.$objMenu["icon"].'"></i> '.$objMenu["rotulo"].'
                              </a>
                              <b class="arrow"></b>
                            </li>';
                }
          }

          echo $html;

         ?>
  </ul><!-- /.nav-list -->

  <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
    <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
  </div>
</div>






