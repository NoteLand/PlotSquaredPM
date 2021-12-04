<?php
declare(strict_types=1);
namespace MyPlot\subcommand;

use MyPlot\forms\MyPlotForm;
use MyPlot\forms\subforms\ClaimForm;
use MyPlot\MyPlot;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ClaimSubCommand extends SubCommand
{
	public function canUse(CommandSender $sender) : bool {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.claim");
	}

	/**
	 * @param Player $sender
	 * @param string[] $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args) : bool {
		$name = "";
		if(isset($args[0])) {
			$name = $args[0];
		}
		$plot = $this->getOwningPlugin()->getPlotByPosition($sender->getPosition());
		if($plot === null) {
			$sender->sendMessage(MyPlot::getPrefix() . TextFormat::RED . $this->translateString("notinplot"));
			return true;
		}
		if($plot->owner != "") {
			if($plot->owner === $sender->getName()) {
				$sender->sendMessage(MyPlot::getPrefix() . TextFormat::RED . $this->translateString("claim.yourplot"));
			}else{
				$sender->sendMessage(MyPlot::getPrefix() . TextFormat::RED . $this->translateString("claim.alreadyclaimed", [$plot->owner]));
			}
			return true;
		}
		$maxPlots = $this->getOwningPlugin()->getMaxPlotsOfPlayer($sender);
		$plotsOfPlayer = 0;
		foreach($this->getOwningPlugin()->getPlotLevels() as $level => $settings) {
			$level = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName((string)$level);
			if($level !== null and $level->isLoaded()) {
				$plotsOfPlayer += count($this->getOwningPlugin()->getPlotsOfPlayer($sender->getName(), $level->getFolderName()));
			}
		}
		if($plotsOfPlayer >= $maxPlots) {
			$sender->sendMessage(MyPlot::getPrefix() . TextFormat::RED . $this->translateString("claim.maxplots", [$maxPlots]));
			return true;
		}
		$economy = $this->getOwningPlugin()->getEconomyProvider();
		if($economy !== null and !$economy->reduceMoney($sender, $plot->price)) {
			$sender->sendMessage(MyPlot::getPrefix() . TextFormat::RED . $this->translateString("claim.nomoney"));
			return true;
		}
		if($this->getOwningPlugin()->claimPlot($plot, $sender->getName(), $name)) {
			$sender->sendMessage(MyPlot::getPrefix() . $this->translateString("claim.success"));
		}else{
			$sender->sendMessage(MyPlot::getPrefix() . TextFormat::RED . $this->translateString("error"));
		}
		return true;
	}

	public function getForm(?Player $player = null) : ?MyPlotForm {
		if($player !== null and MyPlot::getInstance()->isLevelLoaded($player->getPosition()->getWorld()->getFolderName()))
			return new ClaimForm($player);
		return null;
	}
}