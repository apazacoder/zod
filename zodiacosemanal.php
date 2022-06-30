<?php
/*
Plugin Name: Zodiaco semanal
Plugin URI: https://apazim.com
Description: Sirve para definir horóscopos semanales fácilmente
Version: 1.1
Author: Alcides Apaza
Author URI: https://apazim.com
License: GPLv2
*/

// desde ahora se realizarán cálculos con la zona horaria de Bs. Aires
date_default_timezone_set( 'America/Argentina/Buenos_Aires' );

// Database creation
global $zs_db_version;
$zs_db_version = '1.0';
global $app_prefix;
global $wpdb;
$app_prefix = $wpdb->prefix . 'zs_';

function zs_install() {
	global $wpdb;
	global $zs_db_version;
	global $app_prefix;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $app_prefix . 'signos';
	$sql1       = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,				
		nombre tinytext NOT NULL,						
		slug varchar(30) NOT NULL,						
		PRIMARY KEY  (id),
        KEY  (slug)
	) $charset_collate;";

	$table_name = $app_prefix . 'tipos';
	$sql2       = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,				
		nombre tinytext NOT NULL,
		slug varchar(30) NOT NULL,						
		PRIMARY KEY  (id),
		KEY  (slug)
	) $charset_collate;";

	$table_name = $app_prefix . 'horoscopos';
	$sql3       = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		fecha timestamp DEFAULT 0,
		texto varchar(2048) NOT NULL,
		id_tipo mediumint(9) NOT NULL,
		id_signo mediumint(9) NOT NULL,														
		PRIMARY KEY  (id),
		KEY  (id_tipo),
		KEY  (id_signo)
	) $charset_collate;";

  $table_name = $app_prefix . 'horoscopos_semana';
  $sql4       = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		fecha timestamp DEFAULT 0,
		texto varchar(2048) NOT NULL,
		id_tipo mediumint(9) NOT NULL,
		id_signo mediumint(9) NOT NULL,														
		PRIMARY KEY  (id),
		KEY  (id_tipo),
		KEY  (id_signo)
	) $charset_collate;";

  $table_name = $app_prefix . 'horoscopos_mes';
  $sql5       = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		fecha timestamp DEFAULT 0,
		texto varchar(2048) NOT NULL,
		id_tipo mediumint(9) NOT NULL,
		id_signo mediumint(9) NOT NULL,														
		PRIMARY KEY  (id),
		KEY  (id_tipo),
		KEY  (id_signo)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql1 );
	dbDelta( $sql2 );
	dbDelta( $sql3 );
	dbDelta( $sql4 );
	dbDelta( $sql5 );

	add_option( 'zs_db_version', $zs_db_version );
}

function zs_install_data() {
	global $wpdb;
	global $app_prefix;

	$signos = [
		'Aries',
		'Tauro',
		'Géminis',
		'Cáncer',
		'Leo',
		'Virgo',
		'Libra',
		'Escorpio',
		'Sagitario',
		'Capricornio',
		'Acuario',
		'Piscis'
	];

	$slugs = [
		'aries',
		'tauro',
		'geminis',
		'cancer',
		'leo',
		'virgo',
		'libra',
		'escorpio',
		'sagitario',
		'capricornio',
		'acuario',
		'piscis'
	];

	$table_name = $app_prefix . 'signos';
	for ( $i = 0; $i <= count( $signos ); $i ++ ) {
		$wpdb->insert( $table_name, [
				'nombre' => $signos[ $i ],
				'slug'   => $slugs[ $i ]
			]
		);
	}

	$tipos = [
		'Salud',
		'Trabajo y dinero',
		'Amor',
    'Numeros',
	];

	$slugs = [
		'sal',
		'tyd',
		'amo',
    'nos',
	];

	$table_name = $app_prefix . 'tipos';
	for ( $i = 0; $i <= count( $tipos ); $i ++ ) {
		$wpdb->insert( $table_name, [
				'nombre' => $tipos[ $i ],
				'slug'   => $slugs[ $i ]
			]
		);
	}
}

register_activation_hook( __FILE__, 'zs_install' );
register_activation_hook( __FILE__, 'zs_install_data' );

// custom styles
add_action( 'wp_enqueue_scripts', 'zs_enqueue_scripts' );
function zs_enqueue_scripts( $hook ) {
	wp_enqueue_style( 'zodiacostyle',
		plugins_url( 'stylesheet.css', __FILE__ ) );

	wp_enqueue_script( 'cachescript',
		plugins_url( 'cache.js?'.time(), __FILE__ ) );

	wp_enqueue_script( 'zodiacoscript',
		plugins_url( 'script.js', __FILE__ ) );
}

// hook to add custom admin files
add_action( 'admin_enqueue_scripts', 'zs_admin_enqueue_scripts', 10000 );
function zs_admin_enqueue_scripts( $hook ) {

	// load only if we are in the menú created
	if ( $hook == 'toplevel_page_horoscopos_semanales' ) {
		wp_register_script( 'base_script', plugins_url( 'base.js', __FILE__ ), '', true );
		wp_enqueue_script( 'base_script' );

		wp_enqueue_style( 'base_css', plugins_url( 'bulma.min.css', __FILE__ ) );
		wp_enqueue_style( 'custom_css', plugins_url( 'custom.css', __FILE__ ) );

		wp_register_script( 'custom_script', plugins_url( 'custom.js', __FILE__ ), '', true );
		wp_enqueue_script( 'custom_script' );
	}
}

// add admin page
add_action( 'admin_menu', 'zs_horoscopos_semanales_menu' );
function zs_horoscopos_semanales_menu() {
	add_menu_page( 'Horoscopos semanales', 'Horoscopos semanales', 'manage_options', 'horoscopos_semanales', 'render_horoscopos_semanales', plugins_url( 'icon.png', __FILE__ ), 2 );
}

// render admin page
function render_horoscopos_semanales() {
	echo <<<PAGE
<div id="zodiaco-app">	
	<div class="box has-background-white-bis">		
		<div class="columns selector-columns">
		    <div class="column" v-for="(semana, seindex) in composicion">
		        <button class="button is-link is-active"
		        :class="semanaActual == seindex ? '': 'is-light'" 
		        v-text="semana.textoSemana"
		        @click="cambiarSemana(seindex)"
		        ></button>
		    </div>		   
		</div>
		<div class="columns selector-columns" 
		v-if="semanaActual != -1">
		    <div class="column" 
		    v-for="(dia, diindex) in composicion[semanaActual].dias">
		        <button class="button is-link is-active"
		        :class="diaActual == diindex ? '': 'is-light'"
		        @click="cambiarDia(diindex)"
		        >
		        	<span v-text="dia.diaSemana"></span> 
		        	<span v-text="dia.diaMes"></span>		        
		        </button>
		    </div>		    
		</div>
		<div class="columns selector-columns"
		v-if="diaActual != -1">
		    <div class="column"
		    v-for="(signo, siindex) in composicion[semanaActual].dias[diaActual].signos"
		    >
		        <button class="button is-link is-active"
		        @click="cambiarSigno(siindex)"
		        :class="signoActual == siindex ? '': 'is-light'"
		        >
		        	<span v-text="signo.nombre"></span> 		        
		        </button>
		    </div>		    
		</div>	
		<p class="has-text-centered"
			v-if="semanaActual != -1 && diaActual != -1 && signoActual != -1"
		>		
	        <strong>
		        Horóscopo del 
		        <span v-text="composicion[semanaActual].dias[diaActual].diaSemana"></span>
		        <span v-text="composicion[semanaActual].dias[diaActual].diaMes"></span>
		        de
		        <span v-text="composicion[semanaActual].dias[diaActual].mes"></span>
		        para
		        <span v-text="composicion[semanaActual].dias[diaActual].signos[signoActual].nombre">	        
				</span>	       
	        </strong> 		
		</p>	
		<div class="columns"
		v-if="signoActual != -1">
			<div class="column"
			v-for="(tipo, tiindex) in composicion[semanaActual].dias[diaActual].signos[signoActual].tipos"
			>
				<h2 class="has-text-centered" 
				v-text="tipo.nombre"></h2>
				<div class="field">
				  <div class="control">
				    <textarea class="textarea is-info" rows="7" 
				    :placeholder="'Horóscopo '+tipo.nombre" 
				    v-model="tipo.texto"
				    @keyup="setModifiedForm()"
				    >				    
					</textarea>
				  </div>
				</div>	
			</div>				
		</div>
		
						<p class="has-text-centered"
			v-if="semanaActual != -1 && diaActual != -1 && signoActual != -1"
		>		
	        <strong>
		        Horóscopo de la  		         
		        <span v-text="composicion[semanaActual].textoSemana.toUpperCase()"></span>
		        PARA
		        <span v-text="composicion[semanaActual].dias[diaActual].signos[signoActual].nombre.toUpperCase()"> 		        	       					       
	        </strong> 		
		</p>	
		<div class="columns"
		v-if="signoActual != -1">
			<div class="column"
			v-for="(tipo, tiindex) in composicionSemana[semanaActual].signos[signoActual].tipos"
			>
				<h2 class="has-text-centered" 
				v-text="tipo.nombre"></h2>
				<div class="field">
				  <div class="control">
				    <textarea class="textarea is-info" rows="7" 
				    :placeholder="'Horóscopo '+tipo.nombre" 
				    v-model="tipo.texto"
				    @keyup="setModifiedForm()"
				    >				    
					</textarea>
				  </div>
				</div>	
			</div>				
		</div>
		
		

				<p class="has-text-centered"
			v-if="mesActual != -1 && diaActual != -1 && signoActual != -1"
		>		
	        <strong>
		        Horóscopo del MES DE  		        
		        <span v-text="composicionMes[mesActual].textoMes.toUpperCase()"></span>
		        PARA
		        <span v-text="composicion[semanaActual].dias[diaActual].signos[signoActual].nombre.toUpperCase()">	        
				</span>	       
	        </strong> 		
		</p>
				<div class="columns selector-columns">
		    <div class="column" v-for="(mes, meindex) in composicionMes">
		        <button class="button is-link is-active"
		        :class="mesActual == meindex ? '': 'is-light'" 
		        v-text="mes.textoMes"
		        @click="cambiarMes(meindex)"
		        ></button>
		    </div>		   
		</div>	
		<div class="columns"
		v-if="signoActual != -1">
			<div class="column"
			v-for="(tipo, tiindex) in composicionMes[mesActual].signos[signoActual].tipos"
			>
				<h2 class="has-text-centered" 
				v-text="tipo.nombre"></h2>
				<div class="field">
				  <div class="control">
				    <textarea class="textarea is-info" rows="7" 
				    :placeholder="'Horóscopo '+tipo.nombre" 
				    v-model="tipo.texto"
				    @keyup="setModifiedForm()"
				    >				    
					</textarea>
				  </div>
				</div>	
			</div>				
		</div>
		
		<div id="notifications" 
		v-if="this.processResponse.hasOwnProperty('errores')">							
			<div class="notification is-success is-light has-text-centered" 
			v-if="this.processResponse['errores'].length === 0">
				<strong>Datos guardados correctamente:</strong>				
				<span v-text="processResponse['actualizacionesCorrectas']"></span>
				actualizaciones correctas, 
				<span v-text="processResponse['actualizacionesEfectivas']"></span>
				actualizaciones efectivas y
				<span v-text="processResponse['insercionesCorrectas']"></span>
				inserciones													        	
			</div>
			<div class="notification is-danger is-light has-text-centered" v-else>
				<strong>
				No se pudo guardar, errores en:
				<span v-text="processResponse['errores'].join(', ')"></span>									
				<br>				
				Intente de nuevo por favor
				</strong>
			</div>
									
		</div>
		<div class="columns">
			<div class="column has-text-centered">
				<button class="button 
				is-success is-normal is-active"
				@click="guardarHoroscopos()"
				:class="processing ? 'is-loading' : ''"
				>Guardar horóscopos</button>
			</div>
		</div>
	</div>	
</div>
PAGE;
}

// API Endpoints
// /wp-json/zs/v1/horoscopos
global $api_prefix;
$api_prefix = "zs/v1";

add_action( 'rest_api_init', function () {
	global $api_prefix;
	register_rest_route( $api_prefix, 'horoscopos', [
		'methods'  => 'GET',
		'callback' => 'compose_horoscopos'
	] );

	register_rest_route( $api_prefix, 'horoscopos', [
		'methods'  => 'POST',
		'callback' => 'store_horoscopos'
	] );

	register_rest_route( $api_prefix, 'horoscopo/(?P<signo>[a-z]+)', [
		'methods'  => 'GET',
		'callback' => 'get_horoscopo_hoy'
	] );



} );

function compose_horoscopos() {
	// first monday timestamp
	$lunesTS = strtotime( 'monday this week' );
	$lunes   = date( "Y-m-d", $lunesTS );

	$horoscopos = get_horoscopos( $lunes );

	$composicion = [];
	$composicionSemana = [];
	$composicionMes = [];
	$signos      = getSignos();
	$tipos       = getTipos();

	// datos para las 4 semanas: composición por día
	for ( $se = 1; $se <= 4; $se ++ ) {
		$objSemana               = new stdClass();
		$objSemana->inicioSemana = $lunes;
		$objSemana->textoSemana  = "Semana del " . getLiteralDate( $lunes, "withoutYear" );

		// semanal
    // $objWeek = clone $objSemana;

		$dias                    = [];
		$dia                     = $lunes;
		for ( $di = 1; $di <= 7; $di ++ ) {
			$diaTS             = strtotime( $dia );
			$objDia            = new stdClass();
			$objDia->diaMes    = date( 'd', $diaTS );
			$objDia->diaSemana = getLiteralWeekDay( date( 'w', $diaTS ) );
			$objDia->mes       = getLiteralMonth( date( 'm', $diaTS ) );
			$objDia->dia       = date( 'Y-m-d', $diaTS );
			$arrSignos         = [];
			foreach ( $signos as $signo ) {
				$objSigno           = new stdClass();
				$objSigno->id_signo = $signo->id;
				$objSigno->nombre   = $signo->nombre;
				$objSigno->slug     = $signo->slug;
				$arrTipos           = [];
				foreach ( $tipos as $tipo ) {
					$objTipo          = new stdClass();
					$objTipo->id_tipo = $tipo->id;
					$objTipo->nombre  = $tipo->nombre;
					$objTipo->slug    = $tipo->slug;
					// acá viene el filtrado desde el resultado de una consulta
					$horoscopoExacto       = get_horoscopo_exacto(
						$horoscopos,
						$objDia->dia,
						$objSigno->id_signo,
						$objTipo->id_tipo
					);
					$objTipo->id_horoscopo = $horoscopoExacto->id;
					$objTipo->texto        = $horoscopoExacto->texto;
					array_push( $arrTipos, $objTipo );
				}
				$objSigno->tipos = $arrTipos;
				array_push( $arrSignos, $objSigno );
			}
			$objDia->signos = $arrSignos;
			array_push( $dias, $objDia );
			$dia = date( 'Y-m-d', strtotime( $dia . ' +1 days' ) );
		}
		$objSemana->dias = $dias;
		array_push( $composicion, $objSemana );

		$lunes = date( 'Y-m-d', strtotime( $lunes . ' +1 weeks' ) );
	}

  $lunesTS = strtotime( 'monday this week' );
  $lunes   = date( "Y-m-d", $lunesTS );
	$horoscopos_semana = get_horoscopos_semana($lunes);

  // datos para las 4 semanas: composición por semana
  for ( $se = 1; $se <= 4; $se ++ ) {
    $objSemana               = new stdClass();
    $objSemana->inicioSemana = $lunes;
    $objSemana->textoSemana  = "Semana del " . getLiteralDate( $lunes, "withoutYear" );
    $diaTS =strtotime( $lunes );
    $objSemana->inicio = date('Y-m-d', $diaTS);

    $arrSignos = [];
    foreach ( $signos as $signo ) {
      $objSigno           = new stdClass();
      $objSigno->id_signo = $signo->id;
      $objSigno->nombre   = $signo->nombre;
      $objSigno->slug     = $signo->slug;
      $arrTipos           = [];
      foreach ( $tipos as $tipo ) {
        $objTipo          = new stdClass();
        $objTipo->id_tipo = $tipo->id;
        $objTipo->nombre  = $tipo->nombre;
        $objTipo->slug    = $tipo->slug;
        // acá viene el filtrado desde el resultado de una consulta
        $horoscopoExacto       = get_horoscopo_exacto_semana(
          $horoscopos_semana,
          $objSemana->inicio,
          $objSigno->id_signo,
          $objTipo->id_tipo
        );
        $objTipo->id_horoscopo = $horoscopoExacto->id;
        $objTipo->texto        = $horoscopoExacto->texto;
        array_push( $arrTipos, $objTipo );
      }
      $objSigno->tipos = $arrTipos;
      array_push( $arrSignos, $objSigno );
    }

    $objSemana->signos = $arrSignos;
    array_push($composicionSemana, $objSemana);

    $lunes = date( 'Y-m-d', strtotime( $lunes . ' +1 weeks' ) );
  }

  // composición de meses


  $mesTS = strtotime('first day of this month');
  $mes = date( "Y-m-d", $mesTS );
  $horoscopos_mes = get_horoscopos_mes($mes); // starting from the first month (current) compose horoscopos

  // datos para las 4 semanas: composición por mes
  for ( $se = 1; $se <= 4; $se ++ ) {
    $objMes               = new stdClass();
    $mesTS =strtotime( $mes );
    $objMes->textoMes  = getLiteralMonth( date('m', $mesTS));
    $objMes->inicio = date('Y-m-d', $mesTS);

    $arrSignos = [];
    foreach ( $signos as $signo ) {
      $objSigno           = new stdClass();
      $objSigno->id_signo = $signo->id;
      $objSigno->nombre   = $signo->nombre;
      $objSigno->slug     = $signo->slug;
      $arrTipos           = [];
      foreach ( $tipos as $tipo ) {
        $objTipo          = new stdClass();
        $objTipo->id_tipo = $tipo->id;
        $objTipo->nombre  = $tipo->nombre;
        $objTipo->slug    = $tipo->slug;
        // acá viene el filtrado desde el resultado de una consulta
        $horoscopoExacto       = get_horoscopo_exacto_mes(
          $horoscopos_mes,
          $objMes->inicio,
          $objSigno->id_signo,
          $objTipo->id_tipo
        );
        $objTipo->id_horoscopo = $horoscopoExacto->id;
        $objTipo->texto        = $horoscopoExacto->texto;
        array_push( $arrTipos, $objTipo );
      }
      $objSigno->tipos = $arrTipos;
      array_push( $arrSignos, $objSigno );
    }

    $objMes->signos = $arrSignos;
    array_push($composicionMes, $objMes);

    $mes = date( 'Y-m-d', strtotime( $mes . ' +1 months' ) );
  }


  $datos["composicion"] = $composicion;
	$datos["composicionSemana"] = $composicionSemana;
	$datos["composicionMes"] = $composicionMes;

	$response = new WP_REST_Response( $datos );
	$response->set_status( 200 );

	return $response;
}


function store_horoscopos( $request ) {
	global $wpdb;
	global $app_prefix;

	$data        = json_decode( $request->get_body(), true );
	$composicion = $data["composicion"];

	$wpdb->query( 'START TRANSACTION' );
	$tabla_horoscopos = $app_prefix . 'horoscopos';

	$cache = "let cache = [";

	$inserciones     = [];
	$actualizaciones = [];
	foreach ( $composicion as $semana ) {
		foreach ( $semana["dias"] as $dia ) {
			$fecha = $dia["dia"];
			foreach ( $dia["signos"] as $signo ) {
				$id_signo   = $signo["id_signo"];
				$slug_signo = $signo["slug"];
				foreach ( $signo["tipos"] as $tipo ) {
					$id_horoscopo = $tipo["id_horoscopo"];
					$id_tipo      = $tipo["id_tipo"];
					$slug_tipo    = $tipo["slug"];
					$texto        = $tipo["texto"];
					if ( $id_horoscopo == 0 ) {
						// create
						$o = $wpdb->insert(
							$tabla_horoscopos,
							[
								'fecha'    => $fecha,
								'texto'    => $texto,
								'id_tipo'  => $id_tipo,
								'id_signo' => $id_signo
							],
							[
								'%s',
								'%s',
								'%d',
								'%d'
							]
						);
						array_push( $inserciones, $o );
					} else {
						// update
						$o = $wpdb->update(
							$tabla_horoscopos,
							[
								'fecha'    => $fecha,
								'texto'    => $texto,
								'id_tipo'  => $id_tipo,
								'id_signo' => $id_signo
							],
							[
								'id' => $id_horoscopo
							],
							[
								'%s',
								'%s',
								'%d',
								'%d'
							],
							[
								'%d'
							]
						);
						array_push( $actualizaciones, $o );
					}
					// temporal data to build cache file

					$texto_encoded = str_replace("\n","\\n",$texto);
					$texto_encoded = str_replace("'","\"",$texto_encoded);
					$cache.= <<<CACHE
    {
        fecha: "$fecha",
        texto:  '$texto_encoded',
        slug_tipo: "$slug_tipo",
        slug_signo: "$slug_signo"
    },
CACHE;

				}
			}
		}
	}

	// cierre del cache
	$cache .= '];';

	$contadorInserciones  = count( $inserciones );
	$insercionesCorrectas = 0;
	foreach ( $inserciones as $o ) {
		if ( ! $o ) {
			break;
		} else {
			$insercionesCorrectas ++;
		}
	}

	$contadorActualizaciones  = count( $actualizaciones );
	$actualizacionesCorrectas = 0;
	$actualizacionesEfectivas = 0;
	foreach ( $actualizaciones as $o ) {
		// it's necessary compare to false, 0 can occur for same data
		if ( $o === false ) {
			break;
		} else {
			$actualizacionesCorrectas ++;
			if ( $o !== 0 ) {
				$actualizacionesEfectivas ++;
			}
		}
	}

	$errores = [];
	if ( $contadorInserciones !== $insercionesCorrectas ) {
		array_push( $errores, "inserciones" );
	}

	if ( $contadorActualizaciones !== $actualizacionesCorrectas ) {
		array_push( $errores, "actualizaciones" );
	}

	if ( ! $errores ) {
		$wpdb->query( 'COMMIT' );
		// write file
		try{
			$fp = fopen( __DIR__.'/cache.js', 'wb' );
			fwrite($fp, $cache);
			fclose($fp);
		}catch (Exception $e){
			$errores[] = "cache";
		}
	} else {
		$wpdb->query( 'ROLLBACK' );
	}

	// result from daily store
	$response_day = [
        'errores'                  => $errores,
        'actualizacionesCorrectas' => $actualizacionesCorrectas,
        'actualizacionesEfectivas' => $actualizacionesEfectivas,
        'insercionesCorrectas'     => $insercionesCorrectas,
    ];

	$composicion_semana = $data["composicion_semana"];
	$response_semana = store_horoscopos_semana($composicion_semana);

	$composicion_mes = $data["composicion_mes"];
    $response_mes = store_horoscopos_mes($composicion_mes);

	$response = new WP_REST_Response(
	    array_merge($response_day, $response_semana, $response_mes)
    );
	$response->set_status( 200 );

	return $response;
}

function store_horoscopos_semana( $composicion ) {
  global $wpdb;
  global $app_prefix;

  $wpdb->query( 'START TRANSACTION' );
  $tabla_horoscopos = $app_prefix . 'horoscopos_semana';

  $cache = "let cache_semana = [";

  $inserciones     = [];
  $actualizaciones = [];
  foreach ( $composicion as $semana ) {
    $fecha = $semana["inicio"];

    foreach ( $semana["signos"] as $signo ) {
        $id_signo   = $signo["id_signo"];
        $slug_signo = $signo["slug"];
        foreach ( $signo["tipos"] as $tipo ) {
          $id_horoscopo = $tipo["id_horoscopo"];
          $id_tipo      = $tipo["id_tipo"];
          $slug_tipo    = $tipo["slug"];
          $texto        = $tipo["texto"];
          if ( $id_horoscopo == 0 ) {
            // create
            $o = $wpdb->insert(
              $tabla_horoscopos,
              [
                'fecha'    => $fecha,
                'texto'    => $texto,
                'id_tipo'  => $id_tipo,
                'id_signo' => $id_signo
              ],
              [
                '%s',
                '%s',
                '%d',
                '%d'
              ]
            );
            array_push( $inserciones, $o );
          } else {
            // update
            $o = $wpdb->update(
              $tabla_horoscopos,
              [
                'fecha'    => $fecha,
                'texto'    => $texto,
                'id_tipo'  => $id_tipo,
                'id_signo' => $id_signo
              ],
              [
                'id' => $id_horoscopo
              ],
              [
                '%s',
                '%s',
                '%d',
                '%d'
              ],
              [
                '%d'
              ]
            );
            array_push( $actualizaciones, $o );
          }
          // temporal data to build cache file

          $texto_encoded = str_replace("\n","\\n",$texto);
          $texto_encoded = str_replace("'","\"",$texto_encoded);
          $cache.= <<<CACHE
    {
        fecha: "$fecha",
        texto:  '$texto_encoded',
        slug_tipo: "$slug_tipo",
        slug_signo: "$slug_signo"
    },
CACHE;

        }
      }

  }

  // cierre del cache
  $cache .= '];';

  $contadorInserciones  = count( $inserciones );
  $insercionesCorrectas = 0;
  foreach ( $inserciones as $o ) {
    if ( ! $o ) {
      break;
    } else {
      $insercionesCorrectas ++;
    }
  }

  $contadorActualizaciones  = count( $actualizaciones );
  $actualizacionesCorrectas = 0;
  $actualizacionesEfectivas = 0;
  foreach ( $actualizaciones as $o ) {
    // it's necessary compare to false, 0 can occur for same data
    if ( $o === false ) {
      break;
    } else {
      $actualizacionesCorrectas ++;
      if ( $o !== 0 ) {
        $actualizacionesEfectivas ++;
      }
    }
  }

  $errores = [];
  if ( $contadorInserciones !== $insercionesCorrectas ) {
    array_push( $errores, "inserciones" );
  }

  if ( $contadorActualizaciones !== $actualizacionesCorrectas ) {
    array_push( $errores, "actualizaciones" );
  }

  if ( ! $errores ) {
    $wpdb->query( 'COMMIT' );
    // write file
    try{
      $fp = fopen( __DIR__.'/cache.js', 'ab' );
      fwrite($fp, $cache);
      fclose($fp);
    }catch (Exception $e){
      $errores[] = "cache_semana";
    }
  } else {
    $wpdb->query( 'ROLLBACK' );
  }

  return  [
    'errores'                  => $errores,
    'actualizacionesCorrectas' => $actualizacionesCorrectas,
    'actualizacionesEfectivas' => $actualizacionesEfectivas,
    'insercionesCorrectas'     => $insercionesCorrectas,
  ];
}


function store_horoscopos_mes( $composicion ) {
    global $wpdb;
    global $app_prefix;

    $wpdb->query( 'START TRANSACTION' );
    $tabla_horoscopos = $app_prefix . 'horoscopos_mes';

    $cache = "let cache_mes = [";

    $inserciones     = [];
    $actualizaciones = [];
    foreach ( $composicion as $mes ) {
        $fecha = $mes["inicio"];

        foreach ( $mes["signos"] as $signo ) {
            $id_signo   = $signo["id_signo"];
            $slug_signo = $signo["slug"];
            foreach ( $signo["tipos"] as $tipo ) {
                $id_horoscopo = $tipo["id_horoscopo"];
                $id_tipo      = $tipo["id_tipo"];
                $slug_tipo    = $tipo["slug"];
                $texto        = $tipo["texto"];
                if ( $id_horoscopo == 0 ) {
                    // create
                    $o = $wpdb->insert(
                        $tabla_horoscopos,
                        [
                            'fecha'    => $fecha,
                            'texto'    => $texto,
                            'id_tipo'  => $id_tipo,
                            'id_signo' => $id_signo
                        ],
                        [
                            '%s',
                            '%s',
                            '%d',
                            '%d'
                        ]
                    );
                    array_push( $inserciones, $o );
                } else {
                    // update
                    $o = $wpdb->update(
                        $tabla_horoscopos,
                        [
                            'fecha'    => $fecha,
                            'texto'    => $texto,
                            'id_tipo'  => $id_tipo,
                            'id_signo' => $id_signo
                        ],
                        [
                            'id' => $id_horoscopo
                        ],
                        [
                            '%s',
                            '%s',
                            '%d',
                            '%d'
                        ],
                        [
                            '%d'
                        ]
                    );
                    array_push( $actualizaciones, $o );
                }
                // temporal data to build cache file

                $texto_encoded = str_replace("\n","\\n",$texto);
                $texto_encoded = str_replace("'","\"",$texto_encoded);
                $cache.= <<<CACHE
    {
        fecha: "$fecha",
        texto:  '$texto_encoded',
        slug_tipo: "$slug_tipo",
        slug_signo: "$slug_signo"
    },
CACHE;
            }
        }
    }

    // cierre del cache
    $cache .= '];';

    $contadorInserciones  = count( $inserciones );
    $insercionesCorrectas = 0;
    foreach ( $inserciones as $o ) {
        if ( ! $o ) {
            break;
        } else {
            $insercionesCorrectas ++;
        }
    }

    $contadorActualizaciones  = count( $actualizaciones );
    $actualizacionesCorrectas = 0;
    $actualizacionesEfectivas = 0;
    foreach ( $actualizaciones as $o ) {
        // it's necessary compare to false, 0 can occur for same data
        if ( $o === false ) {
            break;
        } else {
            $actualizacionesCorrectas ++;
            if ( $o !== 0 ) {
                $actualizacionesEfectivas ++;
            }
        }
    }

    $errores = [];
    if ( $contadorInserciones !== $insercionesCorrectas ) {
        array_push( $errores, "inserciones" );
    }

    if ( $contadorActualizaciones !== $actualizacionesCorrectas ) {
        array_push( $errores, "actualizaciones" );
    }

    if ( ! $errores ) {
        $wpdb->query( 'COMMIT' );
        // write file
        try{
            $fp = fopen( __DIR__.'/cache.js', 'ab' );
            fwrite($fp, $cache);
            fclose($fp);
        }catch (Exception $e){
            $errores[] = "cache_mes";
        }
    } else {
        $wpdb->query( 'ROLLBACK' );
    }

    return  [
        'errores'                  => $errores,
        'actualizacionesCorrectas' => $actualizacionesCorrectas,
        'actualizacionesEfectivas' => $actualizacionesEfectivas,
        'insercionesCorrectas'     => $insercionesCorrectas,
    ];
}



// get 4 week horoscopos starting on a date
/**
 * @param $start_date : date formated Y-m-d
 *
 * @return array|object|null
 */
function get_horoscopos( $start_date ) {
	global $wpdb;
	global $app_prefix;
	$table_signos     = $app_prefix . 'signos';
	$table_tipos      = $app_prefix . 'tipos';
	$table_horoscopos = $app_prefix . 'horoscopos';
	$end_date         = date( 'Y-m-d', strtotime( $start_date . ' +27 days' ) );

	return $wpdb->get_results( "SELECT
	  h.id,
	  DATE_FORMAT(h.fecha, '%Y-%m-%d') as fecha,
	  h.texto,
	  h.id_signo,
	  h.id_tipo,
	  s.nombre as nombre_signo,
	  t.nombre as nombre_tipo
	FROM $table_horoscopos h
	       JOIN $table_signos s
	            ON h.id_signo = s.id
	       JOIN $table_tipos t
	            ON h.id_tipo = t.id
	WHERE h.fecha
  		BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:00'
	ORDER BY h.fecha, s.id, t.id" );
}

// get 4 week horoscopos starting on a date
/**
 * @param $start_date : date formated Y-m-d
 *
 * @return array|object|null
 */
function get_horoscopos_semana( $start_date ) {
  global $wpdb;
  global $app_prefix;
  $table_signos     = $app_prefix . 'signos';
  $table_tipos      = $app_prefix . 'tipos';
  $table_horoscopos = $app_prefix . 'horoscopos_semana';
  $end_date         = date( 'Y-m-d', strtotime( $start_date . ' +27 days' ) );

  return $wpdb->get_results( "SELECT
	  h.id,
	  DATE_FORMAT(h.fecha, '%Y-%m-%d') as fecha,
	  h.texto,
	  h.id_signo,
	  h.id_tipo,
	  s.nombre as nombre_signo,
	  t.nombre as nombre_tipo
	FROM $table_horoscopos h
	       JOIN $table_signos s
	            ON h.id_signo = s.id
	       JOIN $table_tipos t
	            ON h.id_tipo = t.id
	WHERE h.fecha
  		BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:00'
	ORDER BY h.fecha, s.id, t.id" );
}

function get_horoscopos_mes( $start_date ) {
  global $wpdb;
  global $app_prefix;
  $table_signos     = $app_prefix . 'signos';
  $table_tipos      = $app_prefix . 'tipos';
  $table_horoscopos = $app_prefix . 'horoscopos_mes';
  $end_date         = date( 'Y-m-d', strtotime( $start_date . ' +3 months' ) );

  return $wpdb->get_results( "SELECT
	  h.id,
	  DATE_FORMAT(h.fecha, '%Y-%m-%d') as fecha,
	  h.texto,
	  h.id_signo,
	  h.id_tipo,
	  s.nombre as nombre_signo,
	  t.nombre as nombre_tipo
	FROM $table_horoscopos h
	       JOIN $table_signos s
	            ON h.id_signo = s.id
	       JOIN $table_tipos t
	            ON h.id_tipo = t.id
	WHERE h.fecha
  		BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:00'
	ORDER BY h.fecha, s.id, t.id" );
}



/**
 * @param $horoscopos : query result
 * @param $fecha : date in format Y-m-d
 * @param $id_tipo int
 * @param $id_signo int
 *
 * @return string: empty string if not found, horoscopo por tipo if found
 */

function get_horoscopo_exacto( $horoscopos, $fecha, $id_signo, $id_tipo ) {
	$objHoroscopo        = new stdClass();
	$objHoroscopo->id    = 0;
	$objHoroscopo->texto = "";
	foreach ( $horoscopos as $horoscopo ) {
		if ( $horoscopo->fecha == $fecha &&
		     $horoscopo->id_signo == $id_signo &&
		     $horoscopo->id_tipo == $id_tipo
		) {
			$objHoroscopo->id    = $horoscopo->id;
			$objHoroscopo->texto = $horoscopo->texto;

			return $objHoroscopo;
		}
	}

	return $objHoroscopo;
}
function get_horoscopo_exacto_semana( $horoscopos_semana, $fecha, $id_signo, $id_tipo ) {
  $objHoroscopo        = new stdClass();
  $objHoroscopo->id    = 0;
  $objHoroscopo->texto = "";
  foreach ( $horoscopos_semana as $horoscopo ) {
    if ( $horoscopo->fecha == $fecha &&
      $horoscopo->id_signo == $id_signo &&
      $horoscopo->id_tipo == $id_tipo
    ) {
      $objHoroscopo->id    = $horoscopo->id;
      $objHoroscopo->texto = $horoscopo->texto;

      return $objHoroscopo;
    }
  }

  return $objHoroscopo;
}

function get_horoscopo_exacto_mes( $horoscopos_mes, $fecha, $id_signo, $id_tipo ) {
  $objHoroscopo        = new stdClass();
  $objHoroscopo->id    = 0;
  $objHoroscopo->texto = "";
  foreach ( $horoscopos_mes as $horoscopo ) {
    if ( $horoscopo->fecha == $fecha &&
      $horoscopo->id_signo == $id_signo &&
      $horoscopo->id_tipo == $id_tipo
    ) {
      $objHoroscopo->id    = $horoscopo->id;
      $objHoroscopo->texto = $horoscopo->texto;

      return $objHoroscopo;
    }
  }

  return $objHoroscopo;
}


function get_horoscopo_hoy( $request ) {
	global $wpdb;
	$signo = filter_var( $request["signo"], FILTER_SANITIZE_STRING );
	$fecha = date( 'Y-m-d', strtotime( 'now' ) );

	$horoscopo = $wpdb->get_results( "SELECT	  
	  DATE_FORMAT(h.fecha, '%Y-%m-%d') as fecha,
	  h.texto,	  	  
	  s.slug                           as slug_signo,	  
	  t.slug                           as slug_tipo
	FROM wpi3_zs_horoscopos h
	       JOIN wpi3_zs_signos s
	            ON h.id_signo = s.id
	       JOIN wpi3_zs_tipos t
	            ON h.id_tipo = t.id
	WHERE
	  s.slug = '$signo'
	  AND
	  h.fecha
	    BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:00'
	ORDER BY h.fecha, s.id, t.id"
	);

	$response = new WP_REST_Response( $horoscopo );
	$response->set_status( 200 );

	return $response;
}

/**
 * @param $month : according to DateTime/date m format
 *
 * @return string
 */
function getLiteralMonth( $month ) {
	switch ( $month ) {
		case '01':
			$literalMonth = 'Enero';
			break;
		case '02':
			$literalMonth = 'Febrero';
			break;
		case '03':
			$literalMonth = 'Marzo';
			break;
		case '04':
			$literalMonth = 'Abril';
			break;
		case '05':
			$literalMonth = 'Mayo';
			break;
		case '06':
			$literalMonth = 'Junio';
			break;
		case '07':
			$literalMonth = 'Julio';
			break;
		case '08':
			$literalMonth = 'Agosto';
			break;
		case '09':
			$literalMonth = 'Septiembre';
			break;
		case '10':
			$literalMonth = 'Octubre';
			break;
		case '11':
			$literalMonth = 'Noviembre';
			break;
		case '12':
			$literalMonth = 'Diciembre';
			break;
		default:
			$literalMonth = '';
	}

	return $literalMonth;
}

/**
 * @param $weekday : according to DateTime/date w format
 *
 * @return string
 */
function getLiteralWeekday( $weekday ) {
	switch ( $weekday ) {
		case '0':
			$literalWeekday = 'Domingo';
			break;
		case '1':
			$literalWeekday = 'Lunes';
			break;
		case '2':
			$literalWeekday = 'Martes';
			break;
		case '3':
			$literalWeekday = 'Miércoles';
			break;
		case '4':
			$literalWeekday = 'Jueves';
			break;
		case '5':
			$literalWeekday = 'Viernes';
			break;
		case '6':
			$literalWeekday = 'Sábado';
			break;
		default:
			$literalWeekday = '';
	}

	return $literalWeekday;
}

function getLiteralDate( $date = "now", $variant = "none" ) {
	$datetime       = new DateTime( $date );
	$month          = $datetime->format( "m" );
	$day            = $datetime->format( "d" );
	$weekday        = $datetime->format( "w" );
	$year           = $datetime->format( "Y" );
	$literalMonth   = getLiteralMonth( $month );
	$literalWeekday = getLiteralWeekday( $weekday );
	switch ( $variant ) {
		case 'none':
			return $literalWeekday . ' ' . $day . ' de ' . $literalMonth . ' de ' . $year;
			break;
		case 'withoutYear':
			return $day . ' de ' . $literalMonth;
		case 'lower':
			return mb_strtolower( $literalWeekday . ' ' . $day . ' de ' . $literalMonth . ' de ' . $year, 'UTF-8' );
			break;
		case 'upper':
			return mb_strtoupper( $literalWeekday . ' ' . $day . ' de ' . $literalMonth . ' de ' . $year, 'UTF-8' );
			break;
		case 'ucfirst':
			return ucfirst( $literalWeekday ) . mb_strtolower( ' ' . $day . ' de ' . $literalMonth . ' de ' . $year, 'UTF-8' );
			break;
		case 'withoutWeekday':
			return $day . ' de ' . $literalMonth . ' de ' . $year;
			break;
		default:
			return '';
	}
}

function getSignos() {
	global $wpdb;
	global $app_prefix;
	$table_name = $app_prefix . 'signos';

	return $wpdb->get_results( "SELECT
  		id,
  		nombre,
        slug
	FROM
	    $table_name" );
}

function getTipos() {
	global $wpdb;
	global $app_prefix;
	$table_name = $app_prefix . 'tipos';

	return $wpdb->get_results( "SELECT
  		id,
  		nombre,
        slug
	FROM
	    $table_name" );
}


























