<?php
declare(strict_types = 1);

namespace app\Services\Server\Persistence\Storage;

interface Storage
{
    public function persist(int $serverId): void;

    public function retrieve(): ?int;

    public function remove(): void;
}
