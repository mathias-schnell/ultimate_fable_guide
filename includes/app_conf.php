<?php
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    define('APP_ROOT',      dirname(__DIR__));
    define('INCLUDES_PATH', APP_ROOT . '/includes');
    define('DATA_PATH',     APP_ROOT . '/data');
    define('CSS_PATH',      APP_ROOT . '/assets/css');
    define('JS_PATH',       APP_ROOT . '/assets/js');

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $relative_path = substr(realpath(dirname(__DIR__)), strlen(realpath($_SERVER['DOCUMENT_ROOT'])));
    $base_path = trim(str_replace(DIRECTORY_SEPARATOR, '/', $relative_path), '/');
    
    define('BASE_URL', $protocol . $host . '/' . ($base_path ? $base_path . '/' : ''));
    define('ASSETS_URL',    BASE_URL . 'assets');
    define('CSS_URL',       ASSETS_URL . '/css');
    define('JS_URL',        ASSETS_URL . '/js');

    $sections = [
        "class_skills"      => "Class Skills",
        "heroic_skills"     => "Heroic Skills",
        "arcana"            => "Arcana",
        "spells"            => "Spells",
        "verses"            => "Verses",
        "dances"            => "Dances",
        "symbols"           => "Symbols",
        "magiseeds"         => "Magiseeds",
        "invocations"       => "Invocations",
        "psychic_gifts"     => "Psychic Gifts",
        "therioforms"       => "Therioforms",
        "vehicle_modules"   => "Vehicle Modules",
        "weapons"           => "Weapons",
        "shields"           => "Shields",
        "armor"             => "Armor",
        "accessories"       => "Accessories",
    ];

    $filter_tags = [
        "Source Books" => [
            "source-core"            => "Core",
            "source-high_fantasy"    => "High Fantasy",
            "source-natural_fantasy" => "Natural Fantasy",
            "source-techno_fantasy"  => "Techno Fantasy",
        ],

        "Classes" => [
            "class-any"          => "Any Class",
            "class-arcanist"     => "Arcanist",
            "class-chanter"      => "Chanter",
            "class-chimerist"    => "Chimerist",
            "class-commander"    => "Commander",
            "class-dancer"       => "Dancer",
            "class-darkblade"    => "Darkblade",
            "class-elementalist" => "Elementalist",
            "class-entropist"    => "Entropist",
            "class-esper"        => "Esper",
            "class-floralist"    => "Floralist",
            "class-fury"         => "Fury",
            "class-gourmet"      => "Gourmet",
            "class-guardian"     => "Guardian",
            "class-invoker"      => "Invoker",
            "class-loremaster"   => "Loremaster",
            "class-merchant"     => "Merchant",
            "class-mutant"       => "Mutant",
            "class-orator"       => "Orator",
            "class-pilot"        => "Pilot",
            "class-rogue"        => "Rogue",
            "class-sharpshooter" => "Sharpshooter",
            "class-spiritist"    => "Spiritist",
            "class-symbolist"    => "Symbolist",
            "class-tinkerer"     => "Tinkerer",
            "class-wayfarer"     => "Wayfarer",
            "class-weaponmaster" => "Weaponmaster",
        ],

        "Actions & Mechanics" => [
            "attack"       => "Attack",
            "equipment"    => "Equipment",
            "melee"        => "Melee",
            "ranged"       => "Ranged",
            "spell"        => "Spell",
            "study"        => "Study",
            "skill"        => "Skill",
            "status"       => "Status",
            "objective"    => "Objective",
            "hinder"       => "Hinder",
            "guard"        => "Guard",
            "clock"        => "Clock",
            "fabula-point" => "Fabula Point",
            "bond"         => "Bond",
            "critical"     => "Critical",
            "fumble"       => "Fumble",
            "travel"       => "Travel",
            "crisis"       => "Crisis",
        ],

        "Attributes & Resources" => [
            "hp"            => "HP",
            "mp"            => "MP",
            "ip"            => "IP",
            "defense"       => "Defense",
            "magic-defense" => "Magic Defense",
            "initiative"    => "Initiative",
            "MIG"           => "Might (MIG)",
            "DEX"           => "Dexterity (DEX)",
            "INS"           => "Insight (INS)",
            "WLP"           => "Willpower (WLP)",
        ],

        "Damage & Affinities" => [
            "damage"        => "Damage",
            "resistance"    => "Resistance",
            "vulnerability" => "Vulnerability",
            "immunity"      => "Immunity",
            "absorption"    => "Absorption",
        ],

        "Damage Types" => [
            "physical" => "Physical",
            "air"      => "Air",
            "bolt"     => "Bolt",
            "dark"     => "Dark",
            "earth"    => "Earth",
            "fire"     => "Fire",
            "ice"      => "Ice",
            "light"    => "Light",
            "poison"   => "Poison",
        ],

        "Item Categories & Properties" => [
            "multi"      => "Multi",
            "two-handed" => "Two-Handed",
            "two-weapon" => "Two-Weapon",
            "arcane"     => "Arcane",
            "bow"        => "Bow",
            "brawling"   => "Brawling",
            "dagger"     => "Dagger",
            "firearm"    => "Firearm",
            "flail"      => "Flail",
            "heavy"      => "Heavy",
            "spear"      => "Spear",
            "sword"      => "Sword",
            "thrown"     => "Thrown",
            "weapon"     => "Weapon",
            "shield"     => "Shield",
            "armor"      => "Armor",
        ],

        "Class Features & Subsystems" => [
            "trade-point" => "Trade Point",
            "ingredient"  => "Ingredient",
            "invocation"  => "Invocation",
            "symbol"      => "Symbol",
            "module"      => "Module",
            "dance"       => "Dance",
            "gift"        => "Gift",
            "verse"       => "Verse",
            "garden"      => "Garden",
            "therioform"  => "Therioform",
            "vehicle"     => "Vehicle",
            "companion"   => "Companion",
        ]
    ];