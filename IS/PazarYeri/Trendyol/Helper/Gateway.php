<?php

namespace IS\PazarYeri\Trendyol\Helper;

use IS\PazarYeri\Trendyol\Services;

Class GateWay
{

	/**
	 *
	 * @description Trendyol Api Supplier Id
	 *
	 */
	public $apiSupplierId;

	/**
	 *
	 * @description Trendyol Api Kullanıcı Adı
	 *
	 */
	public $apiUsername;

	/**
	 *
	 * @description Trendyol Api Şifre
	 *
	 */
	public $apiPassword;
    /**
	 *
	 * @description Trendyol Test Mode
	 *
	 */
    public $apiTestMode;

    public $data;

	/**
	 *
	 * @description REST Api için kabul edilen servisler
	 *
	 */
	protected $allowedServices = array( 
		'brand'      => 'BrandService',
		'cargo'      => 'CargoService',
		'category'   => 'CategoryService',
		'product'    => 'ProductService',
		'order'      => 'OrderService',
		'webhook'    => 'WebhookService',
		'settlement' => 'SettlementService',
        'question'   => 'QuestionService',
        'claim'      => 'ClaimService',
	);

	/**
	 *
	 * @description REST Api servislerinin ilk çağırma için hazırlanması
	 * @param string 
	 * @return string
     *
	 */
    public function __get($name)
    {
		if (!isset($this->allowedServices[$name])) {
			throw new TrendyolException("Geçersiz Servis!");
		}

		if (isset($this->$name)) {
			return $this->$name;
		}

        // $this->$name deprecated, is alternative

		$this->data[$name] = $this->createServiceInstance($this->allowedServices[$name]);
		return $this->data[$name];
    }

    /**
     *
     * Servis sınıfının ilk örneğini oluşturma
     *
     * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
     * @param string $serviceName
     * @return string 
     *
     */
    protected function createServiceInstance($serviceName)
    {
    	$serviceName = "IS\PazarYeri\Trendyol\Services\\" .  $serviceName;
    	if (!class_exists($serviceName)) {
			throw new TrendyolException("Geçersiz Servis!");
    	}
		return new $serviceName($this->apiSupplierId, $this->apiUsername, $this->apiPassword, $this->apiTestMode);
    }
    
}
