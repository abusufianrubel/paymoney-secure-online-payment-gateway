<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class QrCode extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = ['object_id', 'object_type', 'secret', 'qr_image', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the QR code image path based on object ID and object type.
     *
     * @param int $id         The object ID.
     * @param string $objectType The object type.
     * @return string|null    The QR code image path if found, or null if not found.
     */
    public static function getQrCode($id, $objectType = 'user', $selected = [])
    {
        $query = self::where(['object_id' => $id, 'object_type' => $objectType, 'status' => 'Active']);
        !empty($selected) ? $query->select($selected) : $query->select('*');

        return $query->first();
    }

    /**
     * Create User QR code
     *
     * @param object $user
     * @return void
     */
    public static function createUserQrCode($user)
    {
        $imageName = time() . '.' . 'jpg';

        $formattedPhone = $user->formattedPhone ?? '';
        $secretCode = convert_string('encrypt', 'user' . '-' . $user->email . '-' . $formattedPhone . '-' . Str::random(6));

        $qrCode = new self();
        $qrCode->object_id = $user->id;
        $qrCode->object_type = 'user';
        $qrCode->secret = $secretCode;
        $qrCode->qr_image = $imageName;
        $qrCode->status = 'Active';
        $qrCode->save();

        $imageContent = file_get_contents(generateQrcode($qrCode->secret));

        $manager = new ImageManager(new Driver());
        $img = $manager->read($imageContent);
        $img->save(getDirectory('user_qrcode') . $imageName);

        return $qrCode;
    }

    public static function updateQrCode($user)
    {
        $qrCode  = self::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id', 'secret']);
        if (!empty($qrCode)) {
            $qrCode->status = 'Inactive';
            $qrCode->save();
        }

        return self::createUserQrCode($user);
    }


    /**
     * Create or update a QR code for a given object type
     *
     * @param object $user
     * @param string $objectType
     * @param string $payLink
     * @return void
     * @throws \Exception
     */
    public function updateOrCreateQrCode($user, string $objectType, array $payLink)
    {
        try {
            if (!isset($user->id)) {
                throw new \InvalidArgumentException(__('User is required'));
            }
            if (empty($objectType)) {
                throw new \InvalidArgumentException(__('QR code type is required'));
            }
            $secret = convert_string('encrypt', $payLink['paylink_url']);
            return self::updateOrCreate(
                [
                    'object_id' => $user->id,
                    'object_type' => $objectType,
                ],
                [
                    'secret' => $secret,
                    'qr_image' => $payLink['paylink_code'] . '.jpg',
                    'status' => 'Active',
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception(__('Error updating or creating QR code :x', ['x' => $e->getMessage()]));
        }
    }
}
