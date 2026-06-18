<?php

namespace IS\PazarYeri\Trendyol\Services;

use IS\PazarYeri\Trendyol\Helper\Request;

Class OrderService extends Request
{

	/**
	 *
	 * Default API Url Adresi
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @var string
	 *
	 */
    public $apiUrl = 'https://apigw.trendyol.com/integration/order/sellers/{supplierId}/orders';

	/**
	 *
	 * Stream endpoint URL (cursor-based pagination)
	 * @var string
	 *
	 */
    public $apiStreamUrl = 'https://apigw.trendyol.com/integration/order/sellers/{supplierId}/orders/stream';

	/**
	 *
	 * Request sınıfı için gerekli ayarların yapılması
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 *
	 */
	public function __construct($supplierId, $username, $password,$testmode)
	{
		parent::__construct($this->apiUrl, $supplierId, $username, $password, $testmode);

		if ($testmode) {
			$this->apiStreamUrl = str_replace('apigw.trendyol.com', 'stageapigw.trendyol.com', $this->apiStreamUrl);
		}
	}

	/**
	 *
	 * Trendyol üzerinde siparişleri arar.
	 *
	 * @author Ismail Satilmis <ismaiil_0234@hotmail.com>
	 * @param string $degisken
	 * @return string 
	 *
	 */
	public function orderList($data = array())
	{

		$query = array(
			'startDate'          => array('format' => 'unixTime'),
			'endDate'            => array('format' => 'unixTime'),
			'page'               => '',
			'size'               => '',
			'orderNumber'        => '',
			'status'             => array('required' => array('Created', 'Picking', 'Invoiced', 'Shipped', 'Cancelled', 'Delivered', 'UnDelivered', 'Returned', 'Repack', 'UnSupplied')),
			'orderByField'       => array('required' => array('PackageLastModifiedDate', 'CreatedDate')),
			'orderByDirection'   => array('required' => array('ASC', 'DESC')),
			'shipmentPackagesId' => '',
		);

		return $this->getResponse($query, $data);
	}

	/**
	 *
	 * Sipariş paketlerini cursor tabanlı akış (stream) ile çeker.
	 *
	 * Büyük veri tarama (full scan), periyodik senkronizasyon (polling/cron)
	 * ve tüm siparişlerin dışa aktarılması (export) için tasarlanmıştır.
	 *
	 * - Sayfalama mekanizması page tabanlı DEĞİL, cursor tabanlıdır.
	 * - Yanıtta totalElements, totalPages, page alanları DÖNMEZ.
	 * - Yanıtta hasMore, nextCursor, size alanları döner.
	 * - Son 3 aylık veriye erişilebilir.
	 * - Zaman aralığı maksimum 14 gündür (gönderilmezse son 2 hafta otomatik uygulanır).
	 * - Sıralama lastModifiedDate'e göre DESC sabittir.
	 * - Önerilen kullanım: minimum 5 saniye aralıklarla istek atmak.
	 *
	 * Kullanım akışı:
	 *   1. İlk istekte nextCursor gönderilmez.
	 *   2. Yanıtta hasMore=true ise devam edilir.
	 *   3. nextCursor değeri alınıp sonraki istekte kullanılır.
	 *   4. hasMore=false olduğunda akış tamamlanır.
	 *
	 * @param array $data {
	 *   @type int    $startDate             Unix timestamp (saniye), lastModifiedStartDate olarak iletilir
	 *   @type int    $endDate               Unix timestamp (saniye), lastModifiedEndDate olarak iletilir
	 *   @type string $status                Sipariş durumu filtresi
	 *   @type int    $size                  Sayfa başı kayıt sayısı (maks 200)
	 *   @type string $nextCursor            Önceki yanıttan gelen opaque cursor değeri
	 *   @type string $orderNumber           Sipariş numarası filtresi
	 *   @type string $shipmentPackagesId    Kargo paketi ID filtresi
	 * }
	 * @return object { hasMore: bool, nextCursor: string|null, size: int, content: array }
	 *
	 */
	public function orderStreamList($data = array())
	{
		$this->setApiUrl($this->apiStreamUrl);

		$query = array(
			'startDate'          => array('format' => 'unixTime'),
			'endDate'            => array('format' => 'unixTime'),
			'size'               => '',
			'nextCursor'         => '',
			'status'             => array('required' => array('Created', 'Picking', 'Invoiced', 'Shipped', 'Cancelled', 'Delivered', 'UnDelivered', 'Returned', 'Repack', 'UnSupplied')),
			'orderByField'       => array('required' => array('PackageLastModifiedDate', 'CreatedDate')),
			'orderByDirection'   => array('required' => array('ASC', 'DESC')),
			'orderNumber'        => '',
			'shipmentPackagesId' => '',
		);

		return $this->getResponse($query, $data);
	}

	/**
	 *
	 * Tüm sipariş paketlerini cursor tabanlı akış ile otomatik olarak çeker.
	 *
	 * Bu metod hasMore=false olana dek tüm sayfaları otomatik olarak iter
	 * ve tüm content'i birleştirerek döner. İstekler arasında Trendyol'un
	 * önerdiği minimum 5 saniyelik bekleme uygulanır.
	 *
	 * @param array   $data          orderStreamList ile aynı parametreler (nextCursor hariç)
	 * @param int     $delaySeconds  İstekler arası bekleme süresi (varsayılan: 5 saniye)
	 * @return array  Tüm siparişlerin birleştirilmiş content dizisi
	 *
	 */
	public function orderStreamListAll($data = array(), $delaySeconds = 5)
	{
		$allContent = array();
		$nextCursor = null;
		$isFirstRequest = true;

		do {
			if (!$isFirstRequest && $delaySeconds > 0) {
				sleep($delaySeconds);
			}

			if ($nextCursor !== null) {
				$data['nextCursor'] = $nextCursor;
			} else {
				unset($data['nextCursor']);
			}

			$response = $this->orderStreamList($data);

			if (!isset($response->content)) {
				break;
			}

			$allContent = array_merge($allContent, (array) $response->content);

			$hasMore    = isset($response->hasMore) ? (bool) $response->hasMore : false;
			$nextCursor = isset($response->nextCursor) ? $response->nextCursor : null;

			$isFirstRequest = false;

		} while ($hasMore && $nextCursor !== null);

		return $allContent;
	}

}
