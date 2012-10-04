<?php
/**
 * Default class for all db tables in the application
 *
 * @category App
 * @package App_Table
 * @copyright company
 */

abstract class App_Table extends Zend_Db_Table_Row_Abstract
{
    /**
     * Update the timestamps
     *
     * @return void
     */
    protected function _update(){
        if(isset($this->updated_at)){
            $this->updated_at = date('Y-m-d H:i:s');
        }
    }
    
    /**
     * Update the timestamps
     *
     * @return void
     */
    protected function _save(){
        if(isset($this->created_at)){
            $this->created_at = date('Y-m-d H:i:s');
        }
    }
    
    /**
     * Remove a file from the CDN and S3
     *
     * @return void
     */
    public function removeFromCDN(){
        //Remove from S3
        App_DI_Container::get('S3StorageEngine')->deleteItem(
            $this->filename, 
            array(
                Zend_Cloud_StorageService_Adapter_S3::BUCKET_NAME => App_DI_Container::get('ConfigObject')->amazon->s3->assets_bucket
            )
        );
        
        //Invalidate the image in the CDN
        if(!App_DI_Container::get('CloudFront')->invalidate($this->filename)){
            App_Logger::log(sprintf('Error removing %s from CDN', $this->filename), Zend_Log::ERR);
        }
    }
}