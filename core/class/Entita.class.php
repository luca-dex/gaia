<?php

/*
 * ©2012 Croce Rossa Italiana
 */

class Entita {
    
    protected
            $db     = null,
            $cache  = null,
            $_v     = [],
            $_cacheable = true;
    
    private static
            $_t     = 'nomeEntita',
            $_dt    = null;
    
    public
            $id;
    
    public function __construct ( $id = null ) {
        global $db, $cache;
        $this->db = $db;
        if ( $this->_cacheable ) {
            $this->cache = $cache;
        }
        /* Check esistenza */
        if ( self::_esiste($id) ) {
            /* Scaricamento */
            $this->id = $id;
            $q = $this->db->prepare("
                SELECT * FROM ". static::$_t ." WHERE id = :id");
            $q->bindParam(':id', $this->id);
            $q->execute();
            $this->_v = $q->fetch(PDO::FETCH_ASSOC);
        } elseif ( $id === null ) {
            /* Creazione nuovo */
            $this->_crea();
            $this->__construct($this->id);
        } else {
            /* Errore non esistente! */
            $e = new Errore(1003);
            $e->extra = static::$_t. ':' . $id;
            throw $e;
        }
    }
    
    public static function by($_nome, $_valore) {
        global $db;
        $entita = get_called_class();
        $q = $db->prepare("
            SELECT id FROM ". static::$_t . " WHERE $_nome = :valore");
        $q->bindParam(':valore', $_valore);
        $q->execute();
        $r = $q->fetch(PDO::FETCH_NUM);
        if (!$r) { return false; }
        return new $entita($r[0]);
    }

    public static function filtra($_array, $_order = null) {
        global $db;
        $entita = get_called_class();
        $_condizioni = [];
        foreach ( $_array as $_elem ) {
            if ( $_elem[1] == null ) {
                $_condizioni[] = "{$_elem[0]} IS NULL";
            } else {
                $_condizioni[] = "{$_elem[0]} = '{$_elem[1]}'";
            }
        }
        $stringa = implode(' AND ', $_condizioni);
        if ( $_order ) {
            $_order = 'ORDER BY ' . $_order;
        }
        $q = $db->prepare("
            SELECT id FROM ". static::$_t . " WHERE $stringa $_order");
        $q->execute();
        $t = [];
        while ( $r = $q->fetch(PDO::FETCH_NUM) ) {
            $t[] = new $entita($r[0]);
        }
        return $t;
    }
    
    public static function elenco($ordine = '') {
        global $db;
        $entita = get_called_class();
        if ( $ordine ) { 
            $ordine = 'ORDER BY ' . $ordine;
        }
        $q = $db->prepare("
            SELECT id FROM ". static::$_t . " ". $ordine);
        $q->execute();
        $t = [];
        while ( $r = $q->fetch(PDO::FETCH_NUM) ) {
            $t[] = new $entita($r[0]);
        }
        return $t;
    }
    
    public static function cercaFulltext($query, $campi, $limit = 20, $altroWhere = '') {
        global $db;
        $entita = get_called_class();
        //var_dump(count($campi), str_word_count($campi[0]));
        if (count($campi) == 1 AND str_word_count($query) == 1) {
            $stringa = " WHERE {$campi[0]} LIKE :stringa";
            $query = '%' . $query . '%';
        } else {
            $stringa = " WHERE MATCH (" . implode(', ', $campi) .") AGAINST (:stringa)";
        }
        $q = $db->prepare("
            SELECT id FROM ". static::$_t . " ". $stringa . " " . $altroWhere . " LIMIT 0,$limit");
        $q->bindParam(':stringa', $query);
        $q->execute();
        $t = [];
        while ( $r = $q->fetch(PDO::FETCH_NUM) ) {
            $t[] = new $entita($r[0]);
        }
        return $t;
    }
    
    public function __toString() {
        return $this->id;
    }
    
    public static function _esiste ( $id = null ) {
        if (!$id) { return false; }
        global $db, $cache, $conf;
        if ($cache) {
            if ( $cache->get($conf['db_hash'] . static::$_t . ':' . $id) ) {
                return true;
            }
        }
        $q = $db->prepare("
            SELECT id FROM ". static::$_t ." WHERE id = :id");
        $q->bindParam(':id', $id);
        $q->execute();
        $y = (bool) $q->fetch(PDO::FETCH_NUM);
        if ($cache) {
            $cache->set($conf['db_hash'] . static::$_t . ':' . $id, true);
        }
        
        return $y;
    }
    
    protected function generaId() {
        $q = $this->db->prepare("
            SELECT MAX(id) FROM ". static::$_t );
        $q->execute();
        $r = $q->fetch(PDO::FETCH_NUM);
        if (!$r) { $r[0] = 0; }
        return (int) $r[0] + 1;
    }
    
    protected function _crea () { 
        $this->id = $this->generaId();
        $q = $this->db->prepare("
            INSERT INTO ". static::$_t ."
            (id) VALUES (:id)");
        $q->bindParam(':id', $this->id);
        return $q->execute();
    }
    
    public function __get ( $_nome ) {
        global $conf;
        if ( $this->cache ) {
            if ( $r = $this->cache->get($conf['db_hash'] . static::$_t . ':' . $this->id . ':' . $_nome) ) {
                return $r;
            }
        }
        if (array_key_exists($_nome, $this->_v) ) {
            /* Proprietà interna */
            $q = $this->db->prepare("
                SELECT $_nome FROM ". static::$_t ." WHERE id = :id");
            $q->bindParam(':id', $this->id);
            $q->execute();
            $r = $q->fetch(PDO::FETCH_NUM);
            $r = $r[0];

        } else {
            /* Proprietà collegata */
            $q = $this->db->prepare("
                SELECT valore FROM ". static::$_dt ." WHERE id = :id AND nome = :nome");
            $q->bindParam(':id', $this->id);
            $q->bindParam(':nome', $_nome);
            $q->execute();
            $r = $q->fetch(PDO::FETCH_NUM);
            if ($r) {
                $r = $r[0];
            } else {
                $r = null;
            }
        }
        if ( $this->cache ) {
            $this->cache->set($conf['db_hash'] . static::$_t . ':' . $this->id . ':' . $_nome, $r);
        }
        return $r;
    }
    

    public function __set ( $_nome, $_valore ) {
        global $conf;
        if ( array_key_exists($_nome, $this->_v) ) {
            /* Proprietà interna */
            $q = $this->db->prepare("
                UPDATE ". static::$_t ." SET $_nome = :valore WHERE id = :id");
            $q->bindParam(':valore', $_valore);
            $q->bindParam(':id', $this->id);
            $q->execute();
            $this->_v[$_nome] = $_valore;
        } else {
            /* Proprietà collegata */
            if ( $_valore === null ) {
                /* Cancella */
                $q = $this->db->prepare("
                    DELETE FROM ". static::$_dt ." WHERE id = :id AND nome = :nome");
                $q->bindParam(':id', $this->id);
                $q->bindParam(':nome', $_nome);
                $q->execute();
            } else {
                $prec = $this->$_nome;
                if ( $prec === null ) {
                    /* Insert! */
                    $q = $this->db->prepare("
                        INSERT INTO ". static::$_dt ."
                        (id, nome, valore)
                        VALUES
                        (:id, :nome, :valore)");
                } else {
                    /* Update */
                    $q = $this->db->prepare("
                        UPDATE ". static::$_dt ." SET
                        valore = :valore
                        WHERE id = :id
                        AND   nome = :nome");
                }
                $q->bindParam(':id', $this->id);
                $q->bindParam(':nome', $_nome);
                $q->bindParam(':valore', $_valore);
                $q->execute();                
            }

        }
        if ( $this->cache ) {
            $this->cache->set($conf['db_hash'] . static::$_t . ':' . $this->id . ':' . $_nome, $_valore);
        }
    }
    
    public function cancella() {
        global $conf;
        $this->cancellaDettagli();
        $q = $this->db->prepare("
            DELETE FROM ". static::$_t ." WHERE id = :id");
        $q->bindParam(':id', $this->id);
        $q->execute();
        if ( $this->cache ) {
            $this->cache->delete($conf['db_hash'] . static::$_t . ':' . $this->id);
        }
    }
    
    private function cancellaDettagli() {
        if ( !static::$_dt ) { return true; }
        $q = $this->db->prepare("
            DELETE FROM ". static::$_dt ." WHERE id = :id");
        $q->bindParam(':id', $this->id);
        return $q->execute();
    }

}