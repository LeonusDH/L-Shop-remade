<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Edit;

use app\Entity\User;
use app\Exceptions\Media\Character\InvalidRatioException;
use app\Exceptions\User\UserNotFoundException;
use app\Repository\User\UserRepository;
use app\Services\Media\Character\Skin\Image as SkinImage;
use app\Services\Validation\SkinValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class UploadSkinHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var SkinValidator
     */
    private $validator;

    public function __construct(UserRepository $userRepository, ImageManager $imageManager, SkinValidator $validator)
    {
        $this->userRepository = $userRepository;
        $this->imageManager = $imageManager;
        $this->validator = $validator;
    }

    /**
     * @param int          $userId
     * @param UploadedFile $file
     *
     * @throws UserNotFoundException
     */
    public function handle(int $userId, UploadedFile $file): void
    {
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            throw UserNotFoundException::byId($userId);
        }

        $image = $this->imageManager->make($file);
        $hash = sha1_file($file->getPathname());

        if (!$this->validator->validate($image->width(), $image->height())) {
            throw new InvalidRatioException($image->width(), $image->height());
        }

        $this->move($user, $image, $hash);
    }

    /**
     * @param User $user
     * @param Image  $image
     */
    private function move(User $user, Image $image, string $hash): void
    {
        DB::table('users')->where('id', $user->getId())->update(['skin_hash' => $hash]);
        $image->save(SkinImage::getAbsolutePath($hash));
    }
}
