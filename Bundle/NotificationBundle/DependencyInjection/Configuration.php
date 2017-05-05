<?php

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection;

use CoreShop\Bundle\NotificationBundle\Controller\NotificationRuleController;
use CoreShop\Bundle\NotificationBundle\Doctrine\ORM\NotificationRuleRepository;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleType;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Model\NotificationRule;
use CoreShop\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coreshop_notification');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;
        
        $this->addModelsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('notification_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(NotificationRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(NotificationRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(NotificationRuleRepository::class)->end()
                                        ->scalarNode('is_pimcore_class')->defaultValue(false)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(NotificationRuleController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(NotificationRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
