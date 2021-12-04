<?php
declare(strict_types=1);
namespace MyPlot\subcommand;

use MyPlot\forms\MyPlotForm;
use MyPlot\MyPlot;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

abstract class SubCommand implements PluginOwned
{
	/** @var MyPlot $plugin */
    private $plugin;
	/** @var string $name */
    private $name;

	public function __construct(MyPlot $plugin, string $name) {
        $this->plugin = $plugin;
        $this->name = $name;
    }

    /**
     * @return MyPlot
     */
    public function getOwningPlugin() : Plugin
    {
        return $this->plugin;
    }

    /**
     * @return MyPlot
     */
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }

    /**
     * @param string $str
     * @param (float|int|string)[] $params
     *
     * @param string $onlyPrefix
     * @return string
     */
	protected function translateString(string $str, array $params = [], string $onlyPrefix = null) : string {
        return $this->plugin->getLanguage()->translateString($str, $params, $onlyPrefix);
    }

	public abstract function canUse(CommandSender $sender) : bool;

	public function getUsage() : string {
        $usage = $this->getOwningPlugin()->getFallBackLang()->get($this->name . ".usage"); // TODO: use normal language when command autofill gains support
        return ($usage == $this->name . ".usage") ? "" : $usage;
    }

	public function getName() : string {
        $name = $this->getOwningPlugin()->getLanguage()->get($this->name . ".name");
        return ($name == $this->name . ".name") ? "" : $name;
    }

	public function getDescription() : string {
        $desc = $this->getOwningPlugin()->getLanguage()->get($this->name . ".desc");
        return ($desc == $this->name . ".desc") ? "" : $desc;
    }

	public function getAlias() : string {
        $alias = $this->getOwningPlugin()->getLanguage()->get($this->name . ".alias");
        return ($alias == $this->name . ".alias") ? "" : $alias;
    }

	public abstract function getForm(?Player $player = null) : ?MyPlotForm;

	/**
	 * @param CommandSender $sender
	 * @param string[] $args
	 *
	 * @return bool
	 */
	public abstract function execute(CommandSender $sender, array $args) : bool;
}