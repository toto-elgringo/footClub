<?php

namespace Model\Enum;

enum StaffRole: string {
    case Entraineur = "Entraineur";
    case Préparateur = "Préparateur";
    case Analyste = "Analyste";
}