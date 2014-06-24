<?php 

class Intelipost_Shipping_Block_Import extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	public function curl_get_contents_git($url)
	{
	  $curl = curl_init($url);
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	  $data = curl_exec($curl);
	  curl_close($curl);
	  return $data;
	}

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');

		$app_url = Mage::helper("adminhtml")->getUrl("/system_config/edit/section/carriers", array());

		if(isset($_GET['valAction']) && trim($_GET['valAction']) != '') {
			
			$filename = trim($_GET['valAction']);

			$filedata = $this->curl_get_contents_git('https://raw.githubusercontent.com/intelipost/fallback-tables/master/'.$filename.'.json');
			
			if($filedata != '' && $filedata != 'Not Found') {
				$root_dir_path = Mage::getBaseDir();
				$media_dir_path = $root_dir_path.DIRECTORY_SEPARATOR.'media';
				
				$intelipost_dir_path = $media_dir_path.DIRECTORY_SEPARATOR.'intelipost';
				if (!is_dir($intelipost_dir_path)) { mkdir($intelipost_dir_path); }
				
				$filepath = $intelipost_dir_path.DIRECTORY_SEPARATOR.$filename.".json";
				$fh = fopen($filepath, 'w') or die("can't open file");
				
				fwrite($fh, $filedata);
				fclose($fh);
				
				if($filename != 'state_codification') {
					$filedata = $this->curl_get_contents_git('https://raw.githubusercontent.com/intelipost/fallback-tables/master/state_codification.json');

					$filepath = $intelipost_dir_path.DIRECTORY_SEPARATOR."state_codification.json";
					$fh = fopen($filepath, 'w') or die("can't open file");
					
					fwrite($fh, $filedata);
					fclose($fh);
				}

				$message = $this->__('"'.$filename.'" file has been imported successfully.');
				Mage::getSingleton('adminhtml/session')->addSuccess($message);
			}else{
				$message = $this->__('"'.$filename.'" file have not found in Github fallback repository.');
				Mage::getSingleton('adminhtml/session')->addError($message);
			}

			header("location:".$app_url);
			exit;
		}

		echo("<script> 
			function importFunction() {
				var nmFile = document.getElementById('carriers_intelipost_table_name').value;
				if(nmFile == ''){
					alert('Please enter a fallback filename.');
					document.getElementById('carriers_intelipost_table_name').focus();
					return false;
				}

				setLocation('".$app_url."?valAction='+nmFile);
				return true;
			}
		</script>");
		
		$originalData = $element->getOriginalData();
        $this->setElement($element);
		
		$html = $this->getLayout()->createBlock('adminhtml/widget_button')
		->setType('button')
		->setClass('scalable')
		->setLabel(Mage::helper('intelipost')->__($originalData['button_label']))
		->setOnClick("return importFunction()")
		->toHtml();
        
		return $html;
    }
}

?>