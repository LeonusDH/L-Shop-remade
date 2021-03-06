<?php
declare(strict_types = 1);

namespace app\Services\Purchasing;

use app\Entity\Purchase;
use app\Entity\User;
use app\Events\Purchase\PurchaseCreatedEvent;
use app\Repository\Purchase\PurchaseRepository;
use app\Services\User\Balance\Transactor;
use Illuminate\Events\Dispatcher;

class ReplenishmentCreator
{
    /**
     * @var PurchaseRepository
     */
    private $repository;

    /**
     * @var Transactor
     */
    private $transactor;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(PurchaseRepository $repository, Transactor $transactor, Dispatcher $eventDispatcher)
    {
        $this->repository = $repository;
        $this->transactor = $transactor;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(float $sum, User $user, string $ip): Purchase
    {
        $purchase = (new Purchase($sum, $ip))
            ->setUser($user);
        $this->repository->create($purchase);
        $this->eventDispatcher->dispatch(new PurchaseCreatedEvent($purchase));

        return $purchase;
    }
}
