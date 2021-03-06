<?php
namespace Ecole\Service;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Ecole\Entity\Ecole;
use Zend\Filter\StaticFilter;

/**
 * The PostManager service is responsible for adding new posts, updating existing
 * posts, adding tags to post, etc.
 */
class EcoleManager
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager;
     */
    private $entityManager;
    
    /**
     * The directory where we save image files.
     * @var string
     */
    private $saveToDir = './public/img/upload/logo/';
    
   
    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
     /**
     * Returns path to the directory where we save the image files.
     * @return string
     */
    public function getSaveToDir() 
    {
        return $this->saveToDir;
    }
    
    /**
     * Returns the path to the saved image file.
     * @param string $fileName Image file name (without path part).
     * @return string Path to image file.
     */
    public function getImagePathByName($fileName) 
    {
        // Take some precautions to make file name secure
        $fileName = str_replace("/", "", $fileName);  // Remove slashes
        $fileName = str_replace("\\", "", $fileName); // Remove back-slashes
                
        // Return concatenated directory name and file name.
        return $this->saveToDir . $fileName;                
    }
    
     /**
     * Returns the array of saved file names.
     * @return array List of uploaded file names.
     */
    public function getSavedFiles() 
    {
        // The directory where we plan to save uploaded files.
        
        // Check whether the directory already exists, and if not,
        // create the directory.
        if(!is_dir($this->saveToDir)) {
            if(!mkdir($this->saveToDir)) {
                throw new \Exception('Could not create directory for uploads: '. error_get_last());
            }
        }
        
        // Scan the directory and create the list of uploaded files.
        $files = array();        
        $handle  = opendir($this->saveToDir);
        while (false !== ($entry = readdir($handle))) {
            
            if($entry=='.' || $entry=='..')
                continue; // Skip current dir and parent dir.
            
            $files[] = $entry;
        }
        
        // Return the list of uploaded files.
        return $files;
    }
    
    /**
     * Retrieves the file information (size, MIME type) by image path.
     * @param string $filePath Path to the image file.
     * @return array File information.
     */
    public function getImageFileInfo($filePath) 
    {
        // Try to open file        
        if (!is_readable($filePath)) {            
            return false;
        }
                
        // Get file size in bytes.
        $fileSize = filesize($filePath);

        // Get MIME type of the file.
        $finfo = finfo_open(FILEINFO_MIME);
        $mimeType = finfo_file($finfo, $filePath);
        if($mimeType===false)
            $mimeType = 'application/octet-stream';
        
        return [
            'size' => $fileSize,
            'type' => $mimeType 
        ];
    }
    
    /**
     * Returns the image file content. On error, returns boolean false. 
     * @param string $filePath Path to image file.
     * @return string|false
     */
    public function getImageFileContent($filePath) 
    {
        return file_get_contents($filePath);
    }
    
    /**
     * This method adds a new post.
     */
    public function addNewEcole($data, $logo_path) 
    {
        // Create new Salle entity.
        $ecole = new Ecole();
        $ecole->setNom($data['nom']);
        $ecole->setAdresse($data['adresse']);
        $ecole->setEmail($data['email']);
        $ecole->setTelephones($data['telephones']);
        $ecole->setLogo($logo_path);
                       
        // Add the entity to entity manager.
        $this->entityManager->persist($ecole);  
        $this->entityManager->flush();
    }
    
    public function editEcole($ecole, $data) 
    {
        $ecole->setNom($data['nom']);
        $ecole->setAdresse($data['adresse']);
        $ecole->setEmail($data['email']);
        $ecole->setTelephones($data['telephones']);
        // Apply changes to database.
        $this->entityManager->flush();
    }
           
    /**
     * Removes .
     */
    public function deleteEcole($ecole) 
    {
        $this->entityManager->remove($ecole);
        $this->entityManager->flush();
    }
    
     /**
     * Resizes the image, keeping its aspect ratio.
     * @param string $filePath
     * @param int $desiredWidth
     * @return string Resulting file name.
     */
    public  function resizeImage($filePath, $desiredWidth = 120) 
    {
        // Get original image dimensions.
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        // Calculate aspect ratio
        $aspectRatio = $originalWidth/$originalHeight;
        // Calculate the resulting height
        $desiredHeight = $desiredWidth/$aspectRatio;

        // Get image info
        $fileInfo = $this->getImageFileInfo($filePath); 
        
        // Resize the image
        $resultingImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
        
        if (substr($fileInfo['type'], 0, 9) =='image/png')
            $originalImage = imagecreatefrompng($filePath);
        else
            $originalImage = imagecreatefromjpeg($filePath);
        imagecopyresampled($resultingImage, $originalImage, 0, 0, 0, 0, 
                 $desiredWidth, $desiredHeight, $originalWidth, $originalHeight);

        // Save the resized image to temporary location
        $tmpFileName = @tempnam("/tmp", "FOO");
        imagejpeg($resultingImage, $tmpFileName, 80);
        
        // Return the path to resulting image.
        return $tmpFileName;
    }
    
}
