<?php

class FormHandler {
  private $_post;
  private $_errors;

  public function __construct($data) {
    $this->_post    = $data['post'];
    $this->_files   = $data['files'];
    $this->_service = $data['gdrive_service'];
    $this->_errors  = array();
  }

  public function execute() {
    $this->_validate();
    $folder = $this->_createFolder(array('filename' => $this->_post['filename']));
    $this->_createFile($folder->id, $this->_files['pdf_file']);
    echo 'Successfully uploaded file!';
  }

  private function _createFile($folderId, $file) {
    $fileMetaData = new Google_Service_Drive_DriveFile(array(
      'name' => $file['name'],
      'parents' => array($folderId)
    ));
    $content = file_get_contents($file['tmp_name']);
    $createdFile = $this->_service->files->create($fileMetaData, array(
      'data' => $content,
      'mimeType' => $file['type'],
      'uploadType' => 'multipart',
      'fields' => 'id'
    ));
    return $createdFile;
  }

  private function _createFolder($options) {
    $filename = $options['filename'];
    $fileMetaData = new Google_Service_Drive_DriveFile(array(
      'name' => $filename,
      'mimeType' => 'application/vnd.google-apps.folder'
    ));
    $file = $this->_service->files->create($fileMetaData, array('fields' => 'id'));
    return $file;
  }

  private function _validate() {
    $maxsize = 25000000;
    $validTypes = array('application/pdf');
    $file = $this->_files['pdf_file'];
    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (trim($this->_post['filename']) === '') {
      echo 'Error: Please input a valid filename';
      exit();
    }

    if (!in_array($file['type'], $validTypes) || $fileExt !== 'pdf') {
      echo "Error: Invalid file type.";
      exit();
    }

    if ($file['size'] > $maxsize) {
      echo "Error: File too large.";
      exit();
    }
  }
}
