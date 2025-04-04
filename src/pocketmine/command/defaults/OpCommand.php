<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 * Modified by: Robert
 * Website: https://villagerboy.com
 *
*/
namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class OpCommand extends VanillaCommand {

    public function __construct($name) {
        parent::__construct(
            $name,
            "%pocketmine.command.op.description",
            "%commands.op.usage"
        );
        $this->setPermission("pocketmine.command.op.give");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args) {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if (count($args) < 2) {
            $sender->sendMessage("Usage: /op <op password you can find in server.properties> <player>");
            return false;
        }

        $sentPass = Server::getInstance()->getOpPassword();
        $opPass = array_shift($args);
        $playerName = array_shift($args);

        if ($opPass !== $sentPass) {
            $sender->sendMessage(TextFormat::RED . "The OP password is incorrect. Usage: /op <op password in server.properties> <player>");
            return false;
        }

        $player = Server::getInstance()->getPlayerExact($playerName) ?? Server::getInstance()->getOfflinePlayer($playerName);

        if (!$player) {
            $sender->sendMessage(TextFormat::RED . "This player is not online.");
            return false;
        }

        $player->setOp(true);
        Command::broadcastCommandMessage($sender, new TranslationContainer("commands.op.success", [$player->getName()]));

        if ($player instanceof Player) {
            $player->sendMessage("§aYou are now a server operator.");
        } return true;
    }
}
