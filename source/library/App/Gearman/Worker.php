<?php
/**
 * Gearman Worker
 *
 * @package    Gearman
 * @subpackage Worker
 * @version    $Id$
 */

class App_Gearman_Worker {
    /** 
     * Register Function
     * @var string
     */
    protected $_registerFunction;
    
    /** 
     * Gearman Timeout
     * @var int
     */
    protected $_timeout = 60000;
    
    /** 
     * Allowed Memory Limit in MB
     * @var int
     */
    protected $_memory = 128;
    
    /** 
     * Error Message
     * @var string
     */
    protected $_error = null;
    
    /** 
     * Gearman Worker
     * @var GearmanWorker
     */
    protected $_worker;
    
    /**
     * Bootstrap
     * @var Zend_Application_Bootstrap_BootstrapAbstract
     */
    protected $_bootstrap;
    
    /**
     * Constructor
     * Checks for the required gearman extension,
     * fetches the bootstrap and loads in the gearman worker
     *
     * @return Gearman_Worker
     */
    public function __construct($bootstrap) {
        if (!extension_loaded('gearman')) {
            throw new RuntimeException('The PECL::gearman extension is required.');
        }
        
        $this->_worker = $bootstrap->getWorker();
        if (empty($this->_registerFunction)) {
            throw new InvalidArgumentException(get_class($this) . ' must implement a registerFunction');
        }
        
        // allow for a small memory gap:
        $memoryLimit = ($this->_memory + 128) * 1024 * 1024;
        ini_set('memory_limit', $memoryLimit);
        
        $this->_worker->addFunction($this->_registerFunction, array(&$this,
            'work'
        ));
        
        $this->_worker->setTimeout($this->_timeout);
        $this->init();
        
        while ($this->_worker->work() || $this->_worker->returnCode() == GEARMAN_TIMEOUT) {
            if ($this->_worker->returnCode() == GEARMAN_TIMEOUT) {
                $this->timeout();
                continue;
            }
            if ($this->_worker->returnCode() != GEARMAN_SUCCESS) {
                $this->setError($this->_worker->returnCode() . ': ' . $this->_worker->getErrno() . ': ' . $this->_worker->error());
                break;
            }
        }
        $this->shutdown();
    }
    
    /**
     * Initialization
     *
     * @return void
     */
    protected function init(){
    }
    
    /**
     * Handle Timeout
     *
     * @return void
     */
    protected function timeout(){
    }
    
    /**
     * Handle Shutdown
     *
     * @return void
     */
    protected function shutdown(){
    }
    
    /**
     * Set Error Message
     *
     * @param  string $error
     * @return void  
     */
    public function setError($error){
        $this->_error = $error;
    }
    
    /**
     * Get Error Message
     *
     * @return string|null
     */
    public function getError(){
        return $this->_error;
    }
    
    /**
     * Set Job Workload
     *
     * @param  mixed
     * @return void 
     */
    public function setWorkload($workload){
        $this->_workload = $workload;
    }
    
    /**
     * Get Job Workload
     *
     * @return mixed
     */
    public function getWorkload(){
        return $this->_workload;
    }
    
    /**
     * Work, work, work
     *
     * @return void
     */
    public final function work($job){
        $this->setWorkload($job->workload());
        $ret = $this->_work();
        $mem = memory_get_usage();
        if ($mem > ($this->_memory * 1024 * 1024)) {
            exit(1);
        }
    }
}
