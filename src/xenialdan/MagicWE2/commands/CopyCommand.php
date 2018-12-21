<?php

declare(strict_types=1);

namespace xenialdan\MagicWE2\commands;

use pocketmine\command\CommandSender;
use pocketmine\lang\TranslationContainer;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use xenialdan\MagicWE2\API;
use xenialdan\MagicWE2\Loader;

class CopyCommand extends WECommand {
	public function __construct(Plugin $plugin) {
		parent::__construct("/copy", $plugin);
		$this->setPermission("we.command.copy");
		$this->setDescription("Copy an area");
		$this->setUsage("//copy [flags...]");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		/** @var Player $sender */
		$return = $sender->hasPermission($this->getPermission());
		if (!$return) {
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));
			return true;
		}
		$lang = Loader::getInstance()->getLanguage();
		try {
			$session = API::getSession($sender);
			if (is_null($session)) {
				throw new \Exception("No session was created - probably no permission to use " . $this->getPlugin()->getName());
			}
			$selection = $session->getLatestSelection();
			if (is_null($selection)) {
				throw new \Exception("No selection found - select an area first");
			}
			if (!$selection->isValid()) {
				throw new \Exception("The selection is not valid! Check if all positions are set!");
			}
			if ($selection->getLevel() !== $sender->getLevel()) {
				$sender->sendMessage(Loader::$prefix . TextFormat::GOLD . "[WARNING] You are editing in a level which you are currently not in!");
			}
            $return = API::copyAsync($selection, $session, API::flagParser($args));
		} catch (\Exception $error) {
			$sender->sendMessage(Loader::$prefix . TextFormat::RED . "Looks like you are missing an argument or used the command wrong!");
			$sender->sendMessage(Loader::$prefix . TextFormat::RED . $error->getMessage());
            $sender->sendMessage($this->getUsage());
			$return = false;
		} catch (\ArgumentCountError $error) {
			$sender->sendMessage(Loader::$prefix . TextFormat::RED . "Looks like you are missing an argument or used the command wrong!");
			$sender->sendMessage(Loader::$prefix . TextFormat::RED . $error->getMessage());
            $sender->sendMessage($this->getUsage());
			$return = false;
		} catch (\Error $error) {
			$this->getPlugin()->getLogger()->error($error->getMessage());
			$sender->sendMessage(Loader::$prefix . TextFormat::RED . $error->getMessage());
			$return = false;
		} finally {
			return $return;
		}
	}
}
