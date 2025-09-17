// Register them via the existing PluginManager
import OrderRatingPlugin from "./order-rating-plugin/order-rating-plugin.plugin";

const PluginManager = window.PluginManager;
PluginManager.register('OrderRatingPlugin', OrderRatingPlugin);