<?php

namespace juni\laravelmix\models;

use Craft;
use craft\base\Model;

/**
 * laravelmix settings
 */
class Settings extends Model
{
    /**
     * @var string Folder where the manifest file is located.
     */
    public string $publicDir = '@webroot';

    /**
     * @var string Name of the manifest file.
     */
    public string $manifestName = 'mix-manifest.json';

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['publicDir', 'string'],
            ['manifestName', 'string'],
            [['publicDir', 'manifestName'], 'required'],
        ];
    }
}
