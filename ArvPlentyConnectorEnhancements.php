<?php

namespace ArvPlentyConnectorEnhancements;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class ArvPlentyConnectorEnhancements.
 */
class ArvPlentyConnectorEnhancements extends Plugin
{
    /**
     * @param ContainerBuilder $container
     * @param array            $plugins
     *
     * @return bool
     */
    private function pluginExists(ContainerBuilder $container, array $plugins)
    {
        $folders = $container->getParameter('shopware.plugin_directories');

        foreach ($plugins as $pluginName) {
            foreach ($folders as $folder) {
                if (file_exists($folder . 'Backend/' . $pluginName)) {
                    return true;
                }

                if (file_exists($folder . 'Core/' . $pluginName)) {
                    return true;
                }

                if (file_exists($folder . 'Frontend/' . $pluginName)) {
                    return true;
                }

                if (file_exists($folder . $pluginName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param ContainerBuilder $container
     * @param $filename
     */
    private function loadFile(ContainerBuilder $container, $filename)
    {
        if (!is_file($filename)) {
            return;
        }

        $loader = new XmlFileLoader(
            $container,
            new FileLocator()
        );

        $loader->load($filename);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $this->loadFile($container, __DIR__ . '/DependencyInjection/services.xml');

        if ($this->pluginExists($container, ['SwagBundle'])) {
            $this->loadFile($container, __DIR__ . '/DependencyInjection/bundle.xml');
        }

        parent::build($container);
    }
}
