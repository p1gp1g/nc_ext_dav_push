<?php

namespace OCA\DavPush\Controller;

use OCA\DavPush\AppInfo\Application;
use OCA\DavPush\Service\SubscriptionService;
use OCA\DavPush\Transport\TransportManager;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\IRequest;

class SubscriptionController extends Controller {
	use Errors;

	public function __construct(
		private SubscriptionService $service,
		private TransportManager $transportManager,
		private $userId,
		IRequest $request,
	) {
		parent::__construct(Application::APP_ID, $request);
	}

	#[NoAdminRequired]
	#[NoCSRFRequired]
	public function destroy(int $id): JSONResponse {
		return $this->handleNotFound(function () use ($id) {
			$subscription = $this->service->find($this->userId, $id);

			$transport = $this->transportManager->getTransport($subscription->getTransport());

			$transport->deleteSubscription($subscription->getId());
			$this->service->delete($this->userId, $id);
			
			return [
				'success' => True,
			];
		});
	}
}