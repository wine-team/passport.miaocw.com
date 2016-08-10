<?php
class MW_Config extends CI_Config 
{
	public $main_base_url  =  'http://miaow.localhost/';
	public $images_url     =  'http://images.localhost/';
	public $skins_url      =  'http://skins.localhost/';
	public $passport_url   =  'http://passport.localhost/';
	public $help_url       =  'http://help.localhost/';
	public $ucenter_url    =  'http://ucenter.localhost/';
    
     /**
     * 图片上传路径
     * $dirname 文件夹名称
     * $imageName 图片名称
     */
    public function upload_image_path($dirname, $imageName = '')
    {
        if (!empty($imageName)) {
            return dirname(FCPATH).'/images/'.$dirname.'/'.$imageName;
        }
        return dirname(FCPATH).'/images/'.$dirname.'/';
    }
    
    /**
     * 线路图片显示路径
     * $dirname 文件夹名称
     * $imageName 图片名称
     */
    public function show_image_url($dirname, $imageName = '')
    {
        if (!empty($imageName)) {
            return $this->images_url.$dirname.'/'.$imageName;
        }
        return $this->images_url.$dirname.'/';
    }
    
    /**
     * 图片上传路径小图
     * $dirname 文件夹名称
     * $imageName 图片名称
     */
    public function upload_image_thumb_path($dirname, $imageName = '')
    {
        if (!empty($imageName)) {
            $imageName = $this->get_thumb_image_name($imageName);
            return dirname(FCPATH).'/images/'.$dirname.'/'.$imageName;
        }
        return dirname(FCPATH).'/images/'.$dirname.'/';
    }
    
    /**
     * 线路图片显示路径小图
     * $dirname 文件夹名称
     * $imageName 图片名称
     */
    public function show_image_thumb_url($dirname, $imageName = '',$resize='360')
    {
    	if (!empty($imageName)) {
    		$imageName = $this->get_thumb_image_name($imageName,$resize);
    		return $this->images_url.$dirname.'/'.$imageName;
    	}
    	return $this->images_url.$dirname.'/';
    }
    
    /**
     * 根据大图片名称获取小图片路径。
     * @param unknown $imageName
     * @return unknown|string
     */
    private function get_thumb_image_name($imageName,$resize)
    {
    	if (strpos($imageName, '/') === false) {
    		return $imageName;
    	}
    	$imageNamePath = explode('/', $imageName, 2);
    	$oldDirData  = $imageNamePath[0];
    	$oldFileData = explode('.',$imageNamePath[1]);
    	$imageName = $oldDirData.'/thumb/'.$oldFileData[0].'_'.$resize.'x'.$resize.'.'.$oldFileData[1];
    	return $imageName;
    }
    
}