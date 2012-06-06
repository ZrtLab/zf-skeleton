<?php

/**
 * Description of CronJob
 *
 * @author slovacus
 */
class CronJobs_CronJob
{

    public function despublicarAnunciosVencidos()
    {
        $fecHoy = new Zend_Date();
        $db = new App_Db_Table_Abstract();

        $where = "DATE(fh_vencimiento) < CURDATE()
                  AND online = 1;";
        $sql = "SELECT id FROM anuncio_web WHERE " . $where;
        $ids = $db->getAdapter()->fetchCol($sql);
        $sql = "SELECT url_id FROM anuncio_web WHERE " . $where;
        $urls = $db->getAdapter()->fetchCol($sql);

        if (count($ids) == 0) {
            echo "No hay anuncios que despublicar" . PHP_EOL;
            return;
        }
        $sql = "UPDATE anuncio_web 
            SET 
            fh_aviso_baja = '" . $fecHoy->toString('YYYY-MM-dd H:m:s') . "', 
            estado = '" . Application_Model_AnuncioWeb::ESTADO_DADO_BAJA . "', 
            online = 0, 
            estado_publicacion = 0 
            WHERE " . $db->getAdapter()->quoteInto('id IN (?)',
                $ids);
        $db->getAdapter()->query($sql);

        $zl = new ZendLucene();
        foreach ($ids as $id) {
            $zl->eliminarDocumentoAviso($id);
        }
        $this->_cache = Zend_Registry::get('cache');
        foreach ($urls as $url) {
            echo 'eliminacion del Cache avisos x empresa ' . $url . PHP_EOL;
            $this->_cache->remove('anuncio_web_' . $url);
        }
        echo "Despublicar Anuncios Vencidos[OK]" . PHP_EOL;
        $this->actualizarContadoresPortada();
    }

    public function actualizarSlug()
    {
        $_areas = new Application_Model_Area();
        $slugger = new App_Filter_Slug();
        $dataAreas = $_areas->fetchAll()->toArray();
        foreach ($dataAreas as $area) {
            $area['slug'] = $slugger->filter($area['nombre']);
            $_areas->update($area, 'id = ' . $area['id']);
        }
        $_areas = null;

        $_nivelpuesto = new Application_Model_NivelPuesto();
        $slugger = new App_Filter_Slug();
        $dataNivelPuesto = $_nivelpuesto->fetchAll()->toArray();
        foreach ($dataNivelPuesto as $np) {
            $np['slug'] = $slugger->filter($np['nombre']);
            $_nivelpuesto->update($np, 'id = ' . $np['id']);
        }
        $_nivelpuesto = null;

        $_empresa = new Application_Model_Empresa();
        $slugger = new App_Filter_Slug();
        $dataEmpresa = $_empresa->fetchAll()->toArray();
        foreach ($dataEmpresa as $obj) {
            $obj['slug'] = $slugger->filter($obj['razon_social']);
            $_empresa->update($obj, 'id = ' . $obj['id']);
        }
        $_empresa = null;
        return true;
    }

    public function publicarAnunciosAdecsys()
    {
        $_aw = new Application_Model_AnuncioWeb();
        $db = new App_Db_Table_Abstract();
        $helperAviso = new App_Controller_Action_Helper_Aviso();
        $where = "origen='adecsys' 
        AND chequeado = 1
        AND estado = 'pagado'
        AND online = 0
        AND (fh_impreso <= CURDATE() OR fh_impreso IS NULL);";
        $sql = "SELECT DISTINCT id_compra FROM anuncio_web 
                WHERE " . $where;
        $dataAw = $db->getAdapter()->fetchAll($sql);
        foreach ($dataAw as $key => $row) {
            $helperAviso->actualizaValoresCompraAviso($row['id_compra']);
            echo "Compra " . $row['id_compra'] . " Publicado [OK]" . PHP_EOL;
        }
        echo "Se termino el proceso" . PHP_EOL;
    }

    public function publicarAnunciosAdecsysExtemporaneos()
    {
        $_aw = new Application_Model_AnuncioWeb();
        $db = new App_Db_Table_Abstract();
        $helperAviso = new App_Controller_Action_Helper_Aviso();
        $where = "origen = 'adecsys' 
        AND chequeado = 1
        AND estado = 'pagado'
        AND online = 0;";
        $sql = "SELECT DISTINCT id_compra FROM anuncio_web 
                WHERE " . $where;
        $dataAw = $db->getAdapter()->fetchAll($sql);
        foreach ($dataAw as $key => $row) {
            $helperAviso->actualizaValoresCompraAvisoExtem($row['id_compra']);
            echo "Compra " . $row['id_compra'] . " Publicado [OK]" . PHP_EOL;
        }
        echo "Se termino el proceso" . PHP_EOL;
    }

    public function actualizarContadoresPortada()
    {
        echo "Actualizando Contadores de Portada...." . PHP_EOL;
        $db = new App_Db_Table_Abstract();
        $sql = "DROP TABLE IF EXISTS tempArea;";
        $db->getAdapter()->query($sql);
        $sql = "CREATE TEMPORARY TABLE tempEmpresa 
            (SELECT e.id AS idEmpresa, e.razon_social, COUNT(aw.id) AS contador 
            FROM anuncio_web aw 
            RIGHT JOIN `empresa` e ON aw.id_empresa = e.id 
            WHERE online = 1 
            GROUP BY id_empresa);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `empresa` SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `empresa` SET contador_anuncios = 
            (SELECT contador FROM tempEmpresa WHERE idEmpresa = empresa.id);";
        $db->getAdapter()->query($sql);

        $sql = "CREATE TEMPORARY TABLE tempArea 
            (SELECT a.id AS idArea, a.nombre, COUNT(aw.id) AS contador 
            FROM anuncio_web aw 
            RIGHT JOIN `area` a ON aw.id_area = a.id 
            WHERE online = 1 
            GROUP BY id_area);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `area` SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `area` SET contador_anuncios = 
            (SELECT contador FROM tempArea WHERE idArea = area.id);";
        $db->getAdapter()->query($sql);
        $sql = "DROP TABLE IF EXISTS tempnivelpuesto;";
        $db->getAdapter()->query($sql);

        $sql = "CREATE TEMPORARY TABLE tempnivelpuesto 
            (SELECT np.id AS idNivelPuesto, np.nombre, COUNT(aw.id) AS contador 
            FROM anuncio_web aw 
            RIGHT JOIN nivel_puesto np ON aw.id_nivel_puesto = np.id 
            WHERE online = 1 
            GROUP BY id_nivel_puesto);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE nivel_puesto SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE nivel_puesto SET contador_anuncios = 
            (SELECT contador FROM tempnivelpuesto WHERE idNivelPuesto = nivel_puesto.id);";
        $db->getAdapter()->query($sql);
        $sql = "DROP TABLE IF EXISTS tempubigeo;";
        $db->getAdapter()->query($sql);

        $sql = "CREATE TEMPORARY TABLE tempubigeo 
            (SELECT u.id AS idubigeo, u.nombre, COUNT(aw.id) AS contador, u.padre AS padre 
            FROM anuncio_web aw 
            RIGHT JOIN ubigeo u ON aw.id_ubigeo = u.id 
            WHERE online = 1 
            GROUP BY id_ubigeo);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE ubigeo SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE ubigeo SET contador_anuncios = 
            (SELECT contador FROM tempubigeo WHERE idubigeo = ubigeo.id);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE ubigeo SET contador_anuncios = 
            (SELECT SUM(contador) FROM tempubigeo WHERE padre = 3285) WHERE id = 3285;";
        $db->getAdapter()->query($sql);

        $objAnuncioWeb = new Application_Model_AnuncioWeb();
        // Contadores por Fecha de Publicación
        $dataContadoresFechaPub = $objAnuncioWeb->getCantAvisosPorFechaPublicacion();

        $objContadorFechaPublicacion = new Application_Model_ContadorFechaPublicacion();
        $objContadorFechaPublicacion->delete('1=1');

        foreach ($dataContadoresFechaPub as $value) {
            $registro = array(
                'nombre' => $value['msg'],
                'slug' => $value['slug'],
                'dias' => $value['dias'],
                'contador_anuncios' => $value['cant'],
            );
            $objContadorFechaPublicacion->insert($registro);
        }

        // Contadores por Rango de Remuneración
        $dataContadoresRangoRemuneracion = $objAnuncioWeb->getCantAvisosPorRangoRemuneracion();
        $objContadorRangoRemuneracion = new Application_Model_ContadorRangoRemuneracion();
        $objContadorRangoRemuneracion->delete('1=1');

        foreach ($dataContadoresRangoRemuneracion as $value) {
            $registro = array(
                'nombre' => $value['msg'],
                'slug' => $value['slug'],
                'salario_min' => $value['minimo'],
                'salario_max' => $value['maximo'],
                'contador_anuncios' => $value['cant']
            );
            $objContadorRangoRemuneracion->insert($registro);
        }
        echo "Contadores de Portada Actualizados [OK]" . PHP_EOL;
    }

    public function actualizarContadores()
    {
        echo "Actualizando Contadores de Portada...." . PHP_EOL;
        $db = new App_Db_Table_Abstract();

        $sql = "DROP TABLE IF EXISTS tempArea;";
        $db->getAdapter()->query($sql);
        $sql = "CREATE TEMPORARY TABLE tempEmpresa 
            (SELECT e.id AS idEmpresa, e.razon_social, COUNT(aw.id) AS contador 
            FROM anuncio_web aw 
            RIGHT JOIN `empresa` e ON aw.id_empresa = e.id 
            WHERE online = 1 
            GROUP BY id_empresa);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `empresa` SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `empresa` SET contador_anuncios = 
            (SELECT contador FROM tempEmpresa WHERE idEmpresa = empresa.id);";
        $db->getAdapter()->query($sql);

        $sql = "CREATE TEMPORARY TABLE tempArea 
            (SELECT a.id AS idArea, a.nombre, COUNT(aw.id) AS contador 
            FROM anuncio_web aw 
            RIGHT JOIN `area` a ON aw.id_area = a.id 
            WHERE online = 1 
            GROUP BY id_area);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `area` SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE `area` SET contador_anuncios = 
            (SELECT contador FROM tempArea WHERE idArea = area.id);";
        $db->getAdapter()->query($sql);
        $sql = "DROP TABLE IF EXISTS tempnivelpuesto;";
        $db->getAdapter()->query($sql);

        $sql = "CREATE TEMPORARY TABLE tempnivelpuesto 
            (SELECT np.id AS idNivelPuesto, np.nombre, COUNT(aw.id) AS contador 
            FROM anuncio_web aw 
            RIGHT JOIN nivel_puesto np ON aw.id_nivel_puesto = np.id 
            WHERE online = 1 
            GROUP BY id_nivel_puesto);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE nivel_puesto SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE nivel_puesto SET contador_anuncios = 
            (SELECT contador FROM tempnivelpuesto WHERE idNivelPuesto = nivel_puesto.id);";
        $db->getAdapter()->query($sql);
        $sql = "DROP TABLE IF EXISTS tempubigeo;";
        $db->getAdapter()->query($sql);

        $sql = "CREATE TEMPORARY TABLE tempubigeo 
            (SELECT u.id AS idubigeo, u.nombre, COUNT(aw.id) AS contador, u.padre AS padre 
            FROM anuncio_web aw 
            RIGHT JOIN ubigeo u ON aw.id_ubigeo = u.id 
            WHERE online = 1 
            GROUP BY id_ubigeo);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE ubigeo SET contador_anuncios = 0;";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE ubigeo SET contador_anuncios = 
            (SELECT contador FROM tempubigeo WHERE idubigeo = ubigeo.id);";
        $db->getAdapter()->query($sql);
        $sql = "UPDATE ubigeo SET contador_anuncios = 
            (SELECT SUM(contador) FROM tempubigeo WHERE padre = 3285) WHERE id = 3285;";
        $db->getAdapter()->query($sql);

        $objAnuncioWeb = new Application_Model_AnuncioWeb();
        // Contadores por Fecha de Publicación
        $dataContadoresFechaPub = $objAnuncioWeb->getCantAvisosPorFechaPublicacion();

        $objContadorFechaPublicacion = new Application_Model_ContadorFechaPublicacion();
        $objContadorFechaPublicacion->delete('1=1');

        foreach ($dataContadoresFechaPub as $value) {
            $registro = array(
                'nombre' => $value['msg'],
                'slug' => $value['slug'],
                'dias' => $value['dias'],
                'contador_anuncios' => $value['cant'],
            );
            $objContadorFechaPublicacion->insert($registro);
        }

        // Contadores por Rango de Remuneración
        $dataContadoresRangoRemuneracion = $objAnuncioWeb->getCantAvisosPorRangoRemuneracion();
        $objContadorRangoRemuneracion = new Application_Model_ContadorRangoRemuneracion();
        $objContadorRangoRemuneracion->delete('1=1');

        foreach ($dataContadoresRangoRemuneracion as $value) {
            $registro = array(
                'nombre' => $value['msg'],
                'slug' => $value['slug'],
                'salario_min' => $value['minimo'],
                'salario_max' => $value['maximo'],
                'contador_anuncios' => $value['cant']
            );
            $objContadorRangoRemuneracion->insert($registro);
        }

        echo "Contadores de Portada Actualizados [OK]" . PHP_EOL;
        //Contadores de Anuncio_web

        echo "Actualizando Contadores de Anuncio Web...." . PHP_EOL;
        $fc = new App_Controller_Action_Helper_Aviso();
        $objAnuncio = new Application_Model_AnuncioWeb();
        $todosAvisos = $objAnuncio->getAllAvisosProcesoActivo();
        foreach ($todosAvisos as $item) {
            $fc->actualizarPostulantes($item["id"]);
            $fc->actualizarInvitaciones($item["id"]);
            $fc->actualizarNuevasPostulaciones($item["id"]);
        }

        echo "Contadores de Anuncio Web [OK]" . PHP_EOL;
    }

    public function importarTablasAdecsys()
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        $config = Zend_Registry::get('config');
        $options = array();
        if ($config->adecsys->proxy->enabled) {
            $options = $config->adecsys->proxy->param->toArray();
        }
        $ws = new Adecsys_Wrapper($config->adecsys->wsdl, $options);
        $client = $ws->getSoapClient();
        $this->_aptitusObj = new Aptitus_Adecsys($ws, $db);

        // Activar log en consola
        $log = new Zend_Log(new Zend_Log_Writer_Stream('php://output'));
        $this->_aptitusObj->setLog($log);

        define('CONSOLE_PATH', realpath(dirname(__FILE__) . '/../../logs/jobs'));
        $response = $ws->obtenerEspecialidades();

        //echo count($response->EspecialidadBE);exit;
        $remoteEspecialties = array();
        // @codingStandardsIgnoreStart
        foreach ($response->EspecialidadBE as $especialidad) {
            $remoteEspecialties[$especialidad->Esp_Id] = $especialidad->Des_Esp;
        }
        //@codingStandardsIgnoreEnd
        $totalImportados = count($remoteEspecialties);
        //exit;
        file_put_contents(CONSOLE_PATH . '/lastRequest.xml',
            $client->getLastRequest());
        file_put_contents(CONSOLE_PATH . '/lastResponse.xml',
            $client->getLastResponse());
        $objEspecialidad = new Application_Model_Especialidad();
        $db = new App_Db_Table_Abstract();
        $sql = "SELECT id FROM especialidad where id < 9998;";
        $data = $db->getAdapter()->query($sql)->fetchAll();
        $totalActuales = count($data);
        if ($totalImportados > $totalActuales) {
            $dif = $totalImportados - $totalActuales;
            for ($i = 0; $i < $dif; $i++) {
                $totalActuales++;
                $data = array(
                    'id' => $totalActuales,
                    'nombre' => $remoteEspecialties[$totalActuales]
                );
                echo "Agregó " . $remoteEspecialties[$totalActuales] . PHP_EOL;
                $objEspecialidad->insert($data);
            }
        } else {
            echo "No hay actualizaciones" . PHP_EOL;
            ;
        }
        echo "Importar Tablas de Adecsys[OK]" . PHP_EOL;
    }

    public function llenarBufferUrlIds()
    {
        $_tu = new Application_Model_TempUrlId();
        $db = new App_Db_Table_Abstract();

        $genPassword = new App_Controller_Action_Helper_GenPassword();

        //$sql = "SELECT count(url_id) FROM temp_urlid";
        //$cantUrlGenerateds = $db->getAdapter()->fetchCol($sql);
        //$cantUrlGenerateds = $cantUrlGenerateds[0];

        $config = Zend_Registry::get('config');
        $maxUrlIdsToGenerate = $config->maxUrlIdsToGenerate;

        //list($usec, $sec) = explode(' ', microtime());
        //$number = $sec + $usec;
        //echo "empieza. ".$number.PHP_EOL;



        $sqlDos = "SELECT url_id FROM temp_urlid";
        $urlGeneradas = $db->getAdapter()->fetchCol($sqlDos);

        $cantUrlGenerateds = count($urlGeneradas);

        //list($usec, $sec) = explode(' ', microtime());
        //$tiempo = $sec + $usec - $number;
        //echo "trajo consultas. ".$tiempo.PHP_EOL;
        if ($cantUrlGenerateds < $maxUrlIdsToGenerate) {
            $sql = "SELECT url_id FROM anuncio_web";
            $urlRegistradas = $db->getAdapter()->fetchCol($sql);
        }

        while ($cantUrlGenerateds < $maxUrlIdsToGenerate) {
            do {
                $urlId = $genPassword->_genPassword(5);
                /*
                  $sql = "SELECT id FROM anuncio_web

                  WHERE url_id like '".$urlId."'";
                  $idsAnuncio = $db->getAdapter()->fetchCol($sql);


                  $sqlDos = "SELECT url_id FROM temp_urlid
                  WHERE url_id like '".$urlId."'";
                  $urlids = $db->getAdapter()->fetchCol($sqlDos);
                 */

                $existeAnuncio = in_array($urlId, $urlRegistradas);
                $existeGenerado = in_array($urlId, $urlGeneradas);
                //if (count($idsAnuncio) > 0) {
                //if ($existeAnuncio) {
                //    echo $cantUrlGenerateds.": ".$urlId." repetido en tabla avisos".PHP_EOL;
                //}
                //if (count($urlids) > 0) {
                //if ($existeGenerado) {
                //    echo $cantUrlGenerateds.": ".$urlId." repetido en buffer".PHP_EOL;
                //}
            } while ($existeAnuncio || $existeGenerado);
            $_tu->insert(array('url_id' => $urlId));
            $cantUrlGenerateds++;

            //list($usec, $sec) = explode(' ', microtime());
            //$tiempo = $sec + $usec - $number;
            //echo $cantUrlGenerateds.": ".$urlId." time: ".$tiempo.PHP_EOL;

            $urlGeneradas[] = $urlId;
        }

        //list($usec, $sec) = explode(' ', microtime());
        //$number = $sec + $usec - $number;
        //echo $number.PHP_EOL;

        echo "Se termino el proceso" . PHP_EOL;
    }

    // cron de correccion de index de postulantes de la tabla temp_lucene
    /*
     * Pasa que la tabla zendlucene no tenia el campo params del tipo TEXT ,lo tenia como
     * varchar(400) y no guardaba todo los datos asi que se hizo esta funcion para ingresar
     * los datos updateIndexPostulante y recrear esa tabla.
     */
    public function corregirIndexPostulante()
    {
        $objTemp = new Application_Model_TempLucene();
        $result = $objTemp->getPostulantesFaltantes();
        foreach ($result as $item) {
            $namefunction = $item["namefunction"];
            $idupdate = $item["idupdate"];
            $idinsert = $item["idinsert"];
            if ($namefunction == "updateIndexPostulante") {
                $id = $this->getNumeroCorregido($idupdate);
                $modelPostulante = new Application_Model_Postulante();
                $id = -87;
                $objPostulante = $modelPostulante->find($id);
                if ($objPostulante->count() > 0) {
                    //actualizacion --------------------------------------------
                    $arrayZL["idpostulante"] = $objPostulante[0]->id;
                    $arrayZL["foto"] = $objPostulante[0]->path_foto;
                    $arrayZL["nombres"] = $objPostulante[0]->nombres;
                    $arrayZL["apellidos"] = $objPostulante[0]->apellidos;
                    $arrayZL["telefono"] = $objPostulante[0]->telefono;
                    $arrayZL["slug"] = $objPostulante[0]->slug;
                    $arrayZL["sexo"] = $objPostulante[0]->sexo;
                    $fi = new DateTime();
                    $ff = new DateTime($objPostulante[0]->fecha_nac);
                    $arrayZL["edad"] = $ff->diff($fi)->format('%y');
                    $arrayZL["fechanac"] = $objPostulante[0]->fecha_nac;
                    $arrayZL["sexoclaves"] = $objPostulante[0]->sexo;
                    $arrayZL["ubigeoclaves"] = $objPostulante[0]->id_ubigeo;

                    $ubi = new Application_Model_Ubigeo();
                    $r = $ubi->find($objPostulante[0]->id_ubigeo);
                    $arrayZL["ubigeo"] = $r[0]->nombre;

                    $zl = new ZendLucene();
                    $zl->updateIndexPostulante($id, $arrayZL);
                    //-----------------------------------------------------------
                }
            }
        }
        echo "reestructuracion OK" . PHP_EOL;
        $sql =
            "
                DROP TABLE IF EXISTS `temp_lucene`;
                CREATE TABLE `temp_lucene` (
                  `id` INT(8) NOT NULL AUTO_INCREMENT,
                  `tipo` ENUM('avisos','postulantes','postulaciones') DEFAULT NULL,
                  `params` TEXT,
                  `namefunction` VARCHAR(150) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=MYISAM AUTO_INCREMENT=1 COMMENT=''
                                ROW_FORMAT=DEFAULT
                                CHARSET=latin1
                                COLLATE=latin1_swedish_ci;
            ";
        $adapter = new Application_Model_TempLucene();
        $adapter->getAdapter()->query($sql);
        echo "TABLA temp_lucene CREADA NUEVAMENTE" . PHP_EOL;
    }

    public function getNumeroCorregido($n)
    {
        $numeros = "0123456789";
        $nfinal = "";
        $n = trim($n);
        for ($i = 0; $i < strlen($n); $i++) {
            $letra = substr($n, $i, 1);
            $na = count(explode($letra, $numeros));
            if ($na > 1) $nfinal.=$letra;
            else break;
        }
        return $nfinal;
    }

    public function bloquearAvisosXIdEmpresaContadores($strIdEmp)
    {
        $arrayIdEmp = explode(",", $strIdEmp);
        $modelAnuncioWeb = new Application_Model_AnuncioWeb();
        $modelEmpresa = new Application_Model_Empresa();
        $modelUsuEmp = new Application_Model_UsuarioEmpresa();
        $modelUsuario = new Application_Model_Usuario();

        foreach ($arrayIdEmp as $idEmp) {

            $arrayIdEmp = $modelEmpresa->getEmpresa($idEmp);
            $zl = new ZendLucene();
            if ($arrayIdEmp != false) {

                $arrayUsuEmp = $modelUsuEmp->getAdministradores($idEmp);
                foreach ($arrayUsuEmp as $dataUE) {
                    $arrayUsuAdmin[] = $dataUE['id_usuario'];
                }
                $whereUsuEmp = $modelUsuEmp->getAdapter()->quoteInto('id in (?)',
                    $arrayUsuAdmin);
                $val = $modelUsuario->update(array('activo' => 0), $whereUsuEmp);

                echo 'Empresa ' . $idEmp . ' ha sido baneada. ' . PHP_EOL;
                $sqlEmp = $modelAnuncioWeb->getAdapter()
                    ->select()
                    ->from('anuncio_web', array('url_id', 'id'))
                    ->where('id_empresa = ? ', $idEmp)
                    ->where('online = 1')
                    ->where('estado = ?',
                    Application_Model_AnuncioWeb::ESTADO_PAGADO);
                $urls = $modelAnuncioWeb->getAdapter()->fetchAll($sqlEmp);
                $_cache = null;

                foreach ($urls as $dataUrl) {
                    $zl->eliminarDocumentoAviso($dataUrl['id']);
                    echo 'eliminacion del Cache avisos x empresa ' . $dataUrl['url_id'] . PHP_EOL;

                    $this->_cache = Zend_Registry::get('cache');
                    $this->_cache->remove('anuncio_web_' . $dataUrl['url_id']);
                }

                $sql = "UPDATE anuncio_web 
                        SET 
                            online = 0,
                            estado = '" . Application_Model_AnuncioWeb::ESTADO_BANEADO . "', 
                            estado_anterior = '" . Application_Model_AnuncioWeb::ESTADO_PAGADO . "',
                            fh_edicion = SYSDATE(),
                            fh_aviso_baja = SYSDATE()
                        WHERE id IN ( 
                            SELECT tmp.id 
                            FROM (SELECT aw.id
                                FROM anuncio_web AS aw 
                                LEFT JOIN compra AS c ON aw.id_compra = c.id 
                                INNER JOIN producto AS p ON aw.id_producto = p.id 
                                LEFT JOIN anuncio_impreso AS ai ON aw.id_anuncio_impreso = ai.id 
                                WHERE (aw.online = '1') AND (aw.borrador = 0) AND (aw.estado = 'pagado') 
                                AND (aw.id_empresa = " . $idEmp . " ) AND (aw.eliminado = '0') 
                                ORDER BY aw.fh_pub DESC) 
                            AS tmp
                        )";

                $modelAnuncioWeb->getAdapter()->query($sql);

                echo 'Avisos de la empresa ' . $idEmp . ' estan baneados.' . PHP_EOL;
            } else {
                echo 'La empresa ' . $idEmp . ' no existe.' . PHP_EOL;
            }
        }
    }

}
