<?php
namespace App\Controllers ;

use Interop\Container\ContainerInterface;

/**
 * Description of BaseController
 *
 * @author Comtech
 */
abstract class BaseController {
    protected $c;
    
    public function __construct(ContainerInterface $c) {
        return $this->c = $c;
    }
    
    public function getAllOrderBy($table, $orderColumn, $ascOrDesc) {
        return $this->c->db->table($table)->orderBy($orderColumn, $ascOrDesc)->get();
    }
    
    public function getColumn($table, $returnColumn, $operator, $byColumn) {
        return $this->c->db->table($table)->where($returnColumn, $operator, $byColumn)->get();
    }
}