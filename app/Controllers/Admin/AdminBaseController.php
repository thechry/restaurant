<?php
namespace App\Controllers\Admin ;

use Interop\Container\ContainerInterface;

use Slim\Http\UploadedFile;

/**
 * Description of BaseController
 *
 * @author Comtech
 */
abstract class AdminBaseController {
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

    public function initPage($request, $response)
    {
        $data = AdminBaseController::getAllOrderBy('pages', 'id', 'asc');
        
        return $data[0];
    }
    
        /**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory directory to which the file is moved
 * @param UploadedFile $uploaded file uploaded file to move
 * @return string filename of moved file
 */
    function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        
        return $filename;
    }
}