<?php

namespace Model\Enum;

enum PlayerRole: string {
    case Gardien = "Gardien";
    case Défenseur = "Défenseur";
    case Milieu = "Milieu";
    case Attaquant = "Attaquant";
}