<?php
namespace NifaAppsManager\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Log\Log;
use Cake\Utility\Text;

/**
 * Application Entity
 *
 * @property int $id
 * @property string $name
 * @property string $public_key
 * @property string $secret_key
 * @property string $secret_key_hashed
 * @property bool $inactive
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class Application extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => false,
        'client_public_key' => true,
        'client_private_key' => true,
        'client_url' => true
    ];

    protected function _setHostSecretKeyHashed($host_secret_key_hashed) {
        return (new DefaultPasswordHasher)->hash($host_secret_key_hashed);
    }



}
