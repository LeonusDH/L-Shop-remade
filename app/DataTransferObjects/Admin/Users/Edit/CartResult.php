<?php

namespace app\DataTransferObjects\Admin\Users\Edit;

use app\DataTransferObjects\Frontend\Profile\Cart\Distribution;
use app\Services\Response\JsonRespondent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CartResult implements JsonRespondent
{
    /**
     * @var LengthAwarePaginator
     */
    private $paginator;

    /**
     * @var Distribution[]
     */
    private $distributions = [];

    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;
        foreach ($this->paginator->items() as $item) {
            $this->distributions[] = new Distribution($item);
        }
    }

    /**
     * @inheritDoc
     */
    public function response(): array
    {
        return [
            'paginator' => $this->paginator,
            'distributions' => $this->distributions
        ];
    }
}
