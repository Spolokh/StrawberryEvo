<?php
/**
* acResizeImage
* ����� ��� ��������� �������� �����������. ����������� ��������� ������ �������, ����, 
* � ����� �������� ��������.
* @author �������� ������ ���������� <freeron@yandex.ru>
* @version 1.0
*/

class acResizeImage {

	const PNG  = 'image/png';
	const JPEG = 'image/jpeg';
	const GIF  = 'image/gif';

	private $image;
	private $width; 
	private $height;
	private $type;
	
	/**
	* ������������� �������
	* @param $file string	���� � ���������� �����
	*/
	function __construct($file){ //filetype($file)
	
		file_exists($file) or exit("File does not exist");
		
		$this->setType($file) or exit("���� ���� �� �������� ���������");
		$this->openImage($file);
		$this->setSize();
	}
	
	/**
	* ��������� �������� �����������
	*
	* ��� ����������� ��������� �������������.
	* ���� �������� � ������ � ������, ����������� ������ � �����.
	* ���� �������� ���� ������ - ����������� ��������� �� ��� (���������� � � �������)
	*
	* @param $width integer		����� ������ �����������
	* @param $height integer	����� ������ �����������
	* @return object 			������� ������ ������ 
	*/
	function resize($width = false, $height = false){
		/** � ����������� �� ���� �������, ������� � $newSize ����� ������� �����������.*/
		if(is_numeric($width) && is_numeric($height) && $width > 0 && $height > 0):
			$newSize = $this->getSizeByFramework($width, $height);
		elseif(is_numeric($width) && $width > 0):
			$newSize = $this->getSizeByWidth($width);
		elseif(is_numeric($height) && $height > 0):
			$newSize = $this->getSizeByHeight($height); 
		else : 
			$newSize = array($this->width, $this->height);
		endif;
		
		$newImage = imagecreatetruecolor($newSize[0], $newSize[1]); //��������� � ��������� �������, ��������� ����������� ��������
		imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newSize[0], $newSize[1], $this->width, $this->height);
		$this->image = $newImage;
		$this->setSize();
		return $this;
	}
	
	/**
	* ������� ��� ������� �����������.
	* ������� cropSave ��������� ���������� ����������� � ���������� ������� ������ ������ acResizeImage.
	* @param $x0 integer	���������� x ����� ������ �������
	* @param $y0 integer	���������� y ����� ������ �������
	* @param $w integer		������ ���������� �������
	* @param $h integer		������ ���������� �������
	* @return object		������� ������ ������ 
	*/
	function crop($x0 = 0, $y0 = 0, $w = false, $h = false){
		if(!is_numeric($x0) || $x0 < 0 || $x0 >= $this->width) $x0 = 0;
		if(!is_numeric($y0) || $y0 < 0 || $y0 >= $this->height) $y0 = 0;
		if(!is_numeric($w) || $w <= 0 || $w > $this->width - $x0) $w = $this->width - $x0;
		if(!is_numeric($h) || $h <= 0 || $h > $this->height - $y0) $h = $this->height - $y0;
		return $this->cropSave($x0, $y0, $w, $h);
	}
	
	/**
	* �������, ���������� ���������� ������� �� ��������� �����������
	* ������� cropSave ��������� ���������� ����������� � ���������� ������� ������ ������ acResizeImage.
	* ��� ��������� �������������.
	* ���� �� ���� ������ ��������, ����� �������� ����������� ������������ ���������� �������.
	* 
	* @param $x0 integer		���������� x ����� ������ ������� (�� ��������� - false)
	* @param $y0 integer		���������� y ����� ������ ������� (�� ��������� - false)
	* @param $size integer		������� ���������� ���������� �������
	* @return object 			������� ������ ������ 
	*/
	function cropSquare($x0 = false, $y0 = false, $size = false){
		if(!is_numeric($size) || $size <= 0) $size = false;
		if(!is_numeric($x0) && !is_numeric($y0)){
			if($this->width < $this->height){
				$x0 = 0;
				if(!$size || $size > $this->width){
					$size = $this->width;
					$y0 = round(($this->height - $size) / 2);
				} else $y0 = 0;
				
			} else {
				$y0 = 0;
				if(!$size || $size > $this->height){
					$size = $this->height;
					$x0 = round(($this->width - $size) / 2);
				} else $x0 = 0;
			}
			
		} else {
			if(!is_numeric($x0) || $x0 <= 0 || $x0 >= $this->width) $x0 = 0;
			if(!is_numeric($y0) || $y0 <= 0 || $y0 >= $this->height) $y0 = 0;
			if(!$size || $this->width < $size + $x0) $size = $this->width - $x0;
			if(!$size || $this->height < $size + $y0) $size = $this->height - $y0;
		}   return $this->cropSave($x0, $y0, $size, $size);
	}
	
	/**
	* ��������� �������, ����������� ���������� �����������.
	* 
	* @return object	������� ������ ������ 
	*/
	private function cropSave($x0, $y0, $w, $h){
		$newImage = imagecreatetruecolor($w, $h);
		imagecopyresampled($newImage, $this->image, 0, 0, $x0, $y0, $w, $h, $w, $h);
		$this->image = $newImage;
		$this->setSize();
		return $this;
	}
	
	/**
	* ������� ��� �������� �������� �����������.
	* ������� getSizeByThumbnail ���������� ����� ������� �����������.
	* 
	* @param $width integer		������ ���������
	* @param $height integer	������ ���������
	* @param $c integer			����������� ����������...
	* @return object			������� ������ ������ 
	*/
	function thumbnail($width, $height, $c = 2){
		if(!is_numeric($width) || $width <= 0) $width = $this->width;
		if(!is_numeric($height) || $height <= 0) $height = $this->height;
		if(!is_numeric($c) || $c <= 1) $c = 2;
		$newSize = $this->getSizeByThumbnail($width, $height, $c);
		$newImage = imagecreatetruecolor($newSize[0], $newSize[1]);
		imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newSize[0], $newSize[1], $this->width, $this->height);
		$this->image = $newImage;
		$this->setSize();
		return $this;
	}
	
	/**
	* �������, ����������� ����������� � ����
	* @param $path string		����, �� �������� ������� ��������� ����
	* @param $fileName string	��� ������ �����
	* @param $type string		��� ����� (�� ��������� (false) - ��� ��, ��� � � ��������� �����������)
	* @param $rewrite boolean	����, ������������, ����� �� �������������� ����� � ����������� �������
	* @param $quality integer	�������� ����������� ��� JPG-������
	* @return string			����� ������ �����
	*/
	function save($path = '', $fileName, $type = false, $rewrite = false, $quality = 100){
	
		if(trim($fileName) == '' || $this->image === false) return false;
		$type = strtolower($type);
		switch($type){
			case false:
				$savePath = $path.trim($fileName).".".$this->type;
				switch($this->type)
				{
					case 'jpg':
						if(!$rewrite && @file_exists($savePath)) return false;
						if(!is_numeric($quality) || $quality < 0 || $quality > 100) $quality = 100;
						imagejpeg($this->image, $savePath, $quality);
						return $savePath;
					case 'png':
						if(!$rewrite && @file_exists($savePath)) return false;
						imagepng($this->image, $savePath);
						return $savePath;
					case 'gif':
						if(!$rewrite && @file_exists($savePath)) return false;
						imagegif($this->image, $savePath);
						return $savePath;
					default:
						return false;
				}
				break;
			case 'jpg':
				$savePath = $path.trim($fileName).".".$type;
				if(!$rewrite && @file_exists($savePath)) return false;
				if(!is_numeric($quality) || $quality < 0 || $quality > 100) $quality = 100;
				imagejpeg($this->image, $savePath, $quality);
				return $savePath;
			case 'png':
				$savePath = $path.trim($fileName).".".$type;
				if(!$rewrite && @file_exists($savePath)) return false;
				imagepng($this->image, $savePath);
				return $savePath;
			case 'gif':
				$savePath = $path.trim($fileName).".".$type;
				if(!$rewrite && @file_exists($savePath)) return false;
				imagegif($this->image, $savePath);
				return $savePath;
			default:
				return false;
		}
	}
	
	/**
	* ��������� �������, "�����������" ���� � ����������� �� ���� �����������.
	* @param $file string	���� ��������� �����
	*/
	private function openImage($file){
	
		switch($this->type){
			case 'jpg':
				$this->image = @imagecreatefromjpeg($file);
				break;
			case 'png':
				$this->image = @imagecreatefrompng($file);
				break;
			case 'gif':
				$this->image = @imagecreatefromgif($file);
				break;
			default:
				exit("��� �� ��������");
		}
	}
	
	/**
	* ��������� �������, ������������ � ���� type ��� ��������� �����������
	* @param $file string ���� ��������� �����	
	* @return boolean TRUE, ���� ���� �������� ������������. FALSE - � ��������� ������.
	*/
	private function setType($file){
   
        $mime = strtolower(end(explode('.', $file))); 
		//$mime = strtolower(strrchr($file, '.'));
		switch($mime){
			case 'jpeg':
				$this->type = "jpeg";
				return true;
			case 'jpg':
				$this->type = "jpg";
				return true;
			case 'png':
				$this->type = "png";
				return true;
			case 'gif':
				$this->type = "gif";
				return true;
			default:
				return false;
		}
	}
	
	/**
	* ��������� �������, ������������ ������� ��������� �����������
	*/
	private function setSize(){
		$this->width  = imagesx($this->image);
		$this->height = imagesy($this->image);
	}
	
	/**
	* ��������� �������, ������������ ������� ������ ����������� ��� ���������� ��� � �����.
	* ������ ���������� ���������������.
	* @param $width integer		������������ ������ ������ �����������
	* @param $height integer	������������ ������ ������ �����������
	* @return array 			������, ���������� ������� ������ �����������
	*/
	
	private function getSizeByFramework($width, $height){
		if($this->width <= $width && $this->height <= height) 
			return array($this->width, $this->height);
		if($this->width / $width > $this->height / $height){
			$newSize[0] = $width;
			$newSize[1] = round($this->height * $width / $this->width);
		}
		else{
			$newSize[1] = $height;
			$newSize[0] = round($this->width * $height / $this->height);
		}
		return $newSize;
	}
	
	/**
	* ��������� �������, ������������ ������� ������ ����������� ��� ������ �� ������.
	* ������ ���������� ���������������.
	* @param $width integer		������������ ������ ������ �����������
	* @return array 			������, ���������� ������� ������ �����������
	*/
	private function getSizeByWidth($width){
		if($width >= $this->width) return array($this->width, $this->height);
		$newSize[0] = $width;
		$newSize[1] = round($this->height * $width / $this->width);
		return $newSize;
	}
	
	/**
	* ��������� �������, ������������ ������� ������ ����������� ��� ������ �� ������.
	* ������ ���������� ���������������.
	* 
	* @param $height integer	������������ ������ ������ �����������
	* @return array 			������, ���������� ������� ������ �����������
	*/
	private function getSizeByHeight($height){
		if($height >= $this->height) return array($this->width, $this->height);
		$newSize[1] = $height;
		$newSize[0] = round($this->width * $height / $this->height);
		return $newSize;
	}
	
	/**
	* ��������� �������, ������������ ������� ������ ����������� ��� �������� ���������.
	* ������� ��������� ����������� � ��������� �������, �������� ����������� ��������� �.
	* ���� ����� ������ �� ����� �� ������, ������ ������� ��������� ������� ������� ����� ��� � $c ���,
	* �� ������ ���������� �� ������ �������.
	* ���� �� ������ ������� ��������� ������ ������� � 2 * $c ���, �� ������� ����������� � 2 ����.
	* ���� ���� �� ������ �������� ������ ��������������� ������� �������,
	* �����������, �� ��������� �� ������ ������� ��������������� ������� ������� � $c ��� � 2 * $c.
	* ���� ������ ������������ �� �� ������, � �� ������, ����������, ���������� ������� ��������� �����������
	* � ������� ���������, �������� �������.
	* ����� ������� �������� ������ � ������ � ������������ �������.
	* 
	* @param $width integer		������������ ������ ������ �����������
	* @param $height integer	������������ ������ ������ �����������
	* @param $� integer			����������� ����������...
	* @return array 			������, ���������� ������� ������ �����������
	*/
	private function getSizeByThumbnail($width, $height, $c){
	
		if($this->width <= $width && $this->height <= height) 
			return array($this->width, $this->height);
		$realW  = $this->width;
		$realH  = $this->height;
		$rotate = false;
		
		if($width / $realW <= $height / $realH){
			$t      = $realH;
			$realH  = $realW;
			$realW  = $t;
			$t      = $width;
			$width  = $height;
			$height = $t;
			$rotate = true;
		}
		
		$limX = $c * $width;
		$limY = $c * $height;
		$possH = $realH * $width / $realW;
		
		if($realW > $width){
			if($possH <= $limY){
				$newSize[0] = $width;
				$newSize[1] = round($possH);
			}
			else{
				if($possH <= 2 * $limY){
					$newSize[1] = $limY;
					$newSize[0] = $realW * $newSize[1] / $realH;
				}
				else{
					$newSize[0] = $width / 2;
					$newSize[1] = $realH * $newSize[0] / $realW;
				}
			}
		}
		else{
			if($realH <= $limY){
				$newSize[0] = $realW;
				$newSize[1] = $realH;
			}
			else{
				if($realH <= 2 * $limY){
					if($realW * $limY / $realH >= $width / 2)
					{
						$newSize[1] = $limY;
						$newSize[0] = $realW * $limY / $realH;
					}
					else
					{
						$newSize[0] = $width / 2;
						$newSize[1] = $realH * $newSize[0] / $realW;
					}
				}
				else {
					$newSize[0] = $width / 2;
					$newSize[1] = $realH * $newSize[0] / $realW;
				}
			}
		}
		if($rotate){
			$t = $newSize[0];
			$newSize[0] = $newSize[1];
			$newSize[1] = $t;
		}
		return $newSize;
	}
}