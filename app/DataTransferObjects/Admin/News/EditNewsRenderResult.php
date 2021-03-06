<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\News;

use app\Entity\News;
use app\Services\Response\JsonRespondent;

class EditNewsRenderResult implements JsonRespondent
{
    /**
     * @var News
     */
    private $entity;

    public function __construct(News $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function response(): array
    {
        return [
            'title' => $this->entity->getTitle(),
            'content' => $this->entity->getContent()
        ];
    }
}
