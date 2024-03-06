<?php

namespace juni\laravelmix;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use juni\laravelmix\models\Settings;
use juni\laravelmix\services\LaravelMix as LaravelMixService;
use juni\laravelmix\web\twig\MixExtension;

/**
 * LaravelMix plugin
 *
 * @method static LaravelMix getInstance()
 * @method Settings getSettings()
 * @author Jurjen Nieuwenhuis
 * @copyright Jurjen Nieuwenhuis
 * @license MIT
 * @property-read LaravelMixAlias2 $laravelMix
 */
class LaravelMix extends Plugin
{
    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * LaravelMix::$plugin
     *
     * @var LaravelMix
     */
    public static $plugin;

    public string $schemaVersion = '1.0.0';

    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $config = $this->getSettings()->getAttributes();

        Craft::$app->view->registerTwigExtension(new MixExtension(
            $config['publicDir'],
            $config['manifestName'],
        ));
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): string|null
    {
        return Craft::$app->getView()->renderTemplate(
            'laravelmix/settings',
            ['settings' => $this->getSettings()]
        );
    }
}
