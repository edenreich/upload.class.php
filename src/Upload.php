<?php

namespace Source;

use stdClass;

/**
 * Upload class that handles multiple file uploads.
 *
 * @author Eden Reich <eden.reich@gmail.com>
 * @license MIT
 */

class Upload
{
	const KEY = 'fc01e8d00a90c1d392ec45459deb6f12'; // Please set your key for encryption here.

	/**
	 * Stores the uploaded source input.
	 *
	 * @var array
	 */
	protected $_fileInput = [];

	/**
	 * Stores all files.
	 *
	 * @var array
	 */
	protected $_files = [];
	
	/**
	 * Stores all the file names.
	 *
	 * @var array
	 */
	protected $_fileNames = [];

	/**
	 * Stores all the file types.
	 *
	 * @var array
	 */
	protected $_fileTypes = [];
	
	/**
	 * Stores all the file temporary names.
	 *
	 * @var array
	 */
	protected $_fileTempNames = [];
			
	/**
	 * Stores all the file extensions.
	 *
	 * @var array
	 */
	protected $_fileExtensions = [];
			
	/**
	 * Stores all the file errors.
	 *
	 * @var array
	 */
	protected $_fileErrors = [];
	
	/**
	 * Stores all the file sizes.
	 *
	 * @var array
	 */
	protected $_fileSizes = [];

	/**
	 * Stores the path of the upload folder.
	 * by default will be uploaded to root.
	 *
	 * @var array
	 */
	protected $_directoryPath = '/';

	/**
	 * Stores debug messages.
	 *
	 * @var array
	 */
	protected $_debug = [];

	/**
	 * Stores the encrypted file names.
	 *
	 * @var array
	 */
	protected $_encryptedFileNames = [];

	/**
	 * Stores the allowed files extensions.
	 *
	 * @var array
	 */
	protected $_allowedExtensions = ['jpg', 'png'];

	/**
	 * Stores the maximum allowed size to upload.
	 *
	 * @var integer
	 */
	protected $_maxSize = null;
	
	/**
	 * If the upload is multiple files.
	 *
	 * @var boolean
	 */
	protected $_isMultiple = false;

	/**
	 * Stores the file types that should be encrypted.
	 *
	 * @var array
	 */
	protected $_fileTypesToEncrypt = [];

	/**
	 * Stores all custom error messages.
	 *
	 * @var array
	 */
	protected $_customErrorMessages = [];

	/**
	 * Setting all the attributes with file data and check if it's single or multiple upload.
	 *
	 * @return void
	 */
	public function __construct($input = null)
	{
		if (empty($input) || ! isset($_FILES[$input])) {
			$this->_debug[] = 'Invalid Argument';
			return;
		}
		
		if (empty($_FILES[$input]['name'][0])) {
			return;
		}

		$this->_fileInput = $_FILES[$input];
		$this->_isMultiple = $this->isMultiple($input);
		
		$this->_fileNames = $this->_fileInput['name'];
		$this->_fileTypes = $this->_fileInput['type'];
		$this->_fileTempNames = $this->_fileInput['tmp_name'];
		$this->_fileErrors = $this->_fileInput['error'];
		$this->_fileSizes = $this->_fileInput['size'];
		$this->_fileExtensions = $this->getFileExtensions();

		$this->_files = $this->orderFiles($this->_fileInput);
	}

	/**
	 * This method organized the files in a an array of keys for each file.
	 *
	 * @param array | $files
	 * @return array
	 */
	public function orderFiles(Array $files)
	{
		$sortedFiles = []; 
	
		foreach ($files as $property => $values) {
			foreach ($values as $key => $value) {
				$sortedFiles[$key] = ['name' => $files['name'][$key],
						      'encrypted_name' => '',
						      'type' => $files['type'][$key],
						      'extension' => $this->_fileExtensions[$key],
						      'tmp_name' => $files['tmp_name'][$key],
						      'error' => $files['error'][$key],
						      'size' => $files['size'][$key],
						      'encryption' => false,
						      'success' => false,
						      'errorMessage' => ''];
				
			}
		}

		return $sortedFiles;
	}

	/**
	 * This method allow the developer to set some rules for the upload process.
	 *
	 * @param array | $rules
	 * @return $this
	 */
	public function addRules(Array $rules)
	{
		foreach ($rules as $rule => $value) {
			switch ($rule) {
				case 'size':
					$this->_maxSize = @intval($value);
					break;
				case 'extensions':
					if(is_array($value)) {
						$this->_allowedExtensions = $value;
						break;
					}

					if($extensions = explode('|', $value)) {
						$this->_allowedExtensions = $extensions;
						break;
					}

					$this->_allowedExtensions[] = $value;
					break;
				default:
					$this->_debug[] = 'Sorry but this rule you specfied does not exist';
					break;
			}
		}

		return $this;
	}

	/**
	 * This method allows the developer to set custom error messages.
	 *
	 * @param array | $errorMessages
	 * @return void
	 */
	public function customErrorMessages(array $errorMessages)
	{
		foreach ($errorMessages as $ruleName => $customMessage)
		{
			switch ($ruleName)
			{
				case 'size':
					$this->_customErrorMessages[$ruleName] = $customMessage;
					break;
				case 'extensions':
					$this->_customErrorMessages[$ruleName] = $customMessage;
					break;
				default:
					$this->_debug[] = 'Sorry but this rule you specfied does not exist';
					break;
			}
		}
	}

	/**
	 * This method checks if its files or file.
	 *
	 * @param string | $input
	 * @return boolean
	 */
	protected function isMultiple($input)
	{
		if (count($_FILES[$input]['name']) > 1) {
			return true;
		}
		
		return false;
	}

	/**
	 * Get the extentions of the files.
	 *
	 * @return array
	 */
	protected function getFileExtensions()
	{
		$extensions = [];

		foreach ($this->_fileNames as $filename)
		{
			$str = explode('.', $filename);
			$str = end($str);
			$extension = strtolower($str);
			$extensions[] = $extension;
		}
		
		return $extensions;
	}

	/**
	 * Set the path directory where you want to upload the files(if not specfied file/files 
	 * will be uploaded to the current directory).
	 *
	 * @param string | $path
	 * @return $this
	 */
	public function setDirectory($path)
	{
		if (substr($path , -1) == '/') {
			$this->_directoryPath = $path;
		} else {
			$this->_directoryPath = $path . '/';
		}

		return $this;
	}

	/**
	 * start the upload process.
	 *
	 * @return void
	 */
	public function start()
	{
		if (empty($this->_fileInput)) {
			return;
		}


		if (!file_exists($this->_directoryPath)) {
			$this->_debug[] = 'Sorry, but this path does not exists. you can also set create() to true.
									 Example: $this->setDirectory(\'images\')->create(true);';
			return;
		}
			
		foreach ($this->_files as $key => &$file) {
			if ($this->fileIsNotValid($file)) {
	    		continue;
	    	}

			$fileToUpload = ($this->shouldBeEncrypted($file)) ? $this->_directoryPath . $file['encrypted_name']
															  : $this->_directoryPath . $file['name'];

			if (!move_uploaded_file($file['tmp_name'], $fileToUpload)) {
				$file['success'] = false;
	    	} else {
				$file['success'] = true;
	    	}
		}
	}

	/**
	 * This method checks if the file should be encrypted.
	 *
	 * @param array | $file
	 * @return boolean
	 */
	protected function shouldBeEncrypted($file)
	{
		return $file['encryption'] && $this->inOnlyArray($file);
	}

	/**
	 * Checks if only specific file extensions were set.
	 *
	 * @return boolean
	 */
	protected function inOnlyArray($file)
	{
		if (empty($this->_fileTypesToEncrypt)) {
			return $file['encryption'];
		}

		return in_array($file['extension'], $this->_fileTypesToEncrypt);
	}

	/**
	 * This method decrypt the file name based on the key you specfied.
	 *
	 * @param $encryptedName
	 * 
	 * @return String | Decrypted File Name 
	 */
	public function decryptFileName($encryptedName)
	{
		$encryptedName = str_replace('#', '/' , $base64EncodedString);
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, static::KEY, base64_decode($encryptedCode), MCRYPT_MODE_ECB));
	}

	/**
	 * Save the file/files with the random name on the server(optional for security uses).
	 *
	 * @param boolean | $encrypt
	 * @return $this
	 */
	public function encryptFileNames($encrypt = false)
	{
		if ($encrypt == false) {
			return $this;
		}

		if (empty(static::KEY)) {
			$this->_debug[] = 'Please go to Upload.class.php file and set manually a key inside the const KEY
								     of 32 characters to encrypt your files. keep this key in safe place as well. 
								     you can call $this->generateMeAKey() to generate a random 32 characters key';
			return;
		}	
		
		if (! empty($this->_fileInput)) {
			foreach($this->_fileNames as $key => $fileName) {
				$base64EncodedString = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, static::KEY, $fileName, MCRYPT_MODE_ECB));
				$encryptedName = str_replace('/', '#' , $base64EncodedString);
				
				$extension = $this->_fileExtensions[$key];
				$this->_files[$key]['encrypted_name'] = $encryptedName . "." . $extension;
				$this->_files[$key]['encryption'] = true;
			}
		}

		return $this;
	}

	/**
	 * Allow the user to specify which file types to encrypt.
	 *
	 * @param mixed | $types
	 * @return void
	 */
	public function only($types)
	{
		if (is_string($types) && $extensions = explode('|', $types)) {
			$this->_fileTypesToEncrypt = $extensions;
			return;
		}

		if (! is_array($types)) {
			$this->_fileTypesToEncrypt = func_get_args();
		} else {
			$this->_fileTypesToEncrypt = $types;
		}
	}

	/**
	 * This method create the directory if needed.
	 * 
	 * @param boolean | $create
	 * @return void
	 */
	public function create($create = false)
	{
		if ($create == false) {
			return $this;
		}

		if (!file_exists($this->_directoryPath)) {
			mkdir($this->_directoryPath);	
		}
	}

	/**
	 * This method retrieve the allowed extensions.
	 *
	 * @return array
	 */
	public function getAllowedExtensions() 
	{
		return ($this->_allowedExtensions) ?: '';
	}

	/**
	 * This method retrieve the maximum uploading size.
	 *
	 * @return integer
	 */
	public function getMaxUploadingSize()
	{
		return $this->_maxSize;
	}

	/**
	 * Check if extensions allowed
	 *
	 * @return boolean
	 */
	protected function extensionsAllowed(&$file)
	{
		if (empty($this->_allowedExtensions) && empty($this->_fileExtensions)) {
			return;
		}
		
		if (in_array($file['extension'], $this->_allowedExtensions)) {
			return true;
		}
	
		$file['success'] = false;
		$file['errorMessage'] = (isset($this->_customErrorMessages['extensions'])) ? 
												$this->_customErrorMessages['extensions'] :
												"Sorry, but only " . implode( ", " , $this->_allowedExtensions ) . " files are allowed.";
		return false;
	}

	/**
	 * Check if the file size allowed.
	 *
	 * @return boolean
	 */
	protected function maxSizeOk(&$file)
	{
		if (empty($this->_maxSize) && empty($this->_fileSizes)) {
			return;
		}
			
		if ($file['size'] < ($this->_maxSize * 1000)) {
			return true;
		}
		
		$file['errorMessage'] = (isset($this->_customErrorMessages['size'])) ? 
												$this->_customErrorMessages['size'] :
												"Sorry, but your file, " . $file['name'] . ", is too big. maximal size allowed " . $this->_maxSize . " Kbyte";
		
		return false;	
	}

	/**
	 * Check if file validation fails.
	 *
	 * @return boolean
	 */
	protected function fileIsNotValid(&$file)
	{
		if ($file['error'] !== UPLOAD_ERR_OK) {
	    	$this->_debug[] = 'The file ' . $file['name'] . ' couldn\'t be uploaded. Please ensure 
	    							your php.ini file allow this size of files to be uploaded';
	    	$file['errorMessage'] = 'Invalid File: ' . $file['name'];
	    	return false;
	    }

		if ($this->extensionsAllowed($file) && $this->maxSizeOk($file)) {
			return false;
		}
	
		return true;
	}

	/**
	 * This method checks if the upload was unsuccessful.
	 * 
	 * @return boolean
	 */
	public function unsuccessfulFilesHas()
	{
		foreach ($this->_files as $file) {
			if ($file['success'] == false && !empty($file['errorMessage'])) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * This method checks if the upload was successful.
	 * 
	 * @return boolean
	 */
	public function successfulFilesHas()
	{
		foreach ($this->_files as $file) {
			if ($file['success'] == true) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * This method get the errors array to give some feedback to the user.
	 *
	 * @return array
	 */
	public function errorFiles()
	{
		$failedUploads = [];

		foreach ($this->_files as $key => $file) {
			if ($file['success'] == true) {
				continue;
			}
			
			$failedFile = new stdClass;
			
			$failedFile->name = $file['name'];

			if ($this->shouldBeEncrypted($file)) {
				$failedFile->encryptedName = $file['encrypted_name'];
			}
			
			$failedFile->type = $file['type'];
			$failedFile->extension = $file['extension'];
			$failedFile->size = $file['size'];
			$failedFile->error = $file['error'];
			
			if (! empty($file['errorMessage'])) {
				$failedFile->errorMessage = $file['errorMessage'];
			}

			$failedUploads[] = $failedFile;
		}
						
		return $failedUploads;
	}

	/**
	 * This method get the errors array to give some feedback to the user.
	 *
	 * @return array
	 */
	public function successFiles()
	{
		$successfulUploads = [];

		foreach ($this->_files as $key => $file) {
			if ($file['success'] == false) {
				continue;
			}
			
			$successfulFile = new stdClass();
			
			$successfulFile->name = $file['name'];

			if ($this->shouldBeEncrypted($file)) {
				$successfulFile->encryptedName = $file['encrypted_name'];
			}
			
			$successfulFile->type = $file['type'];
			$successfulFile->extension = $file['extension'];
			$successfulFile->size = $file['size'];

			$successfulUploads[] = $successfulFile;
		}
						
		return $successfulUploads;
	}

	/**
	 * This method displays the errors formated nicely with bootstraps.
	 * 
	 * @return void
	 */
	public function displayErrors()
	{
		foreach ($this->errorFiles() as $file) {
	      echo '<div class="alert alert-danger">couldn\'t upload ' . $file->name .'. '. $file->errorMessage . '</div><br/>';
	    }
	}

	/**
	 * This method displays the errors formated nicely with bootstraps.
	 * 
	 * @return void
	 */
	public function displaySuccess()
	{
		foreach ($this->successFiles() as $file) {
	      echo '<div class="alert alert-success">' . $file->name .' uploaded successfuly</div><br/>';
	    }
	}


	/**
	 * This method check if the file is set. normally when the user submits the form.
	 * 
	 * @return boolean
	 */
	public static function formIsSubmitted()
	{
		if (empty($_FILES)) {
			return false;
		}
		
		return true;
	}

	/**
	 * A simple gererator of a random key to use for encrypting 
	 *
	 * @return void
	 */
	public static function generateMeAKey()
	{
		echo md5(uniqid());
	}

	/**
	 * This method get the errors array to give some feedback to the developer.
	 *
	 * @return array
	 */
	public function debug()
	{
		return $this->_debug;
	}
}
