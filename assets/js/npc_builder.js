class NPC {
    constructor() {
        this.DICE_ARRAYS = {
            0: [8,8,8,8],
            1: [10,8,8,6],
            2: [10,10,6,6],
            3: [12,8,6,6],
        };

        this.BASE_SKILLS = {
            beast: { num_skills: 4, skills: [{"use-equipment": false }] },
            construct: { num_skills: 2, affinities: {"AB": [], "IM": ["poison", "poisoned"], "RS": ["earth"], "VU": []} },
            demon: { num_skills: 2, affinities: {"AB": [], "IM": [], "RS": [2], "VU": []} },
            elemental: { num_skills: 2, affinities: {"AB": [], "IM": [1, "poison", "poisoned"], "RS": [], "VU": []} },
            humanoid: { num_skills: 3, skills: [{"use-equipment": true }] },
            monster: { num_skills: 4 },
            plant: { num_skills: 3, affinities: { "AB": [], "IM": ["dazed", "shaken", "enraged"], "RS": [], "VU": ["air|bolt|fire|ice"] } },
            undead: { num_skills: 2, affinities: { "AB": [], "IM": ["dark", "poison", "poisoned"], "RS": [], "VU": ["light"] }, extra: "When an effect would cause an undead creature to recover Hit Points, whoever controls that effect may instead have the undead lose half as many Hit Points." },
        };

        this.state = {
            name: "NPC",
            level: 5,
            species: "humanoid",
            traits: ["", "", "", ""],
            base_dice: { "MIG": 8, "DEX": 8, "INS": 8, "WLP": 8 },
            current_dice: { "MIG": 8, "DEX": 8, "INS": 8, "WLP": 8 },
            affinities: { "AB": [], "IM": [], "RS": [], "VU": [] },
            num_upgrades: 0,
            num_skills: 0,
            attacks: [],
            spells: [],
            skills: [],
            initiative: 0,
            max_hp: 0,
            crisis: 0,
            max_mp: 0,
            defense: 0,
            magic_defense: 0,
            acc_bonus: 0,
            dmg_bonus: 0,
            init_mod: 0,
            def_mod: 0,
            mdef_mod: 0,
        };
    };

    calc_stats() {
        this.num_upgrades = calc_num_upgrades();
        this.acc_bonus = calc_acc_bonus();
        this.dmg_bonus = calc_dmg_bonus() ;
        this.max_hp = calc_max_hp();
        this.crisis = Math.floor(calc_max_hp() / 2);
        this.max_mp = calc_max_mp();
        this.initiative = calc_initiative();
        this.defense = calc_defense();
        this.magic_defense = calc_magic_defense();
    }

    calc_acc_bonus() {
        return Math.floor(this.state.level/10);
    }
    
    calc_defense() {
        return this.state.current_dice["DEX"] + this.state.def_mod;
    }

    calc_dmg_bonus() {
        return Math.floor(this.state.level/20) * 5;
    }

    calc_initiative() {
        return Math.floor((this.state.base_dice["DEX"] + this.state.base_dice["INS"]) / 2) + this.state.init_mod;
    }

    calc_magic_defense() {
        return this.state.current_dice["INS"] + this.state.mdef_mod;
    }

    calc_max_hp() {
        return (this.state.level * 2) + (this.state.base_dice["MIG"] * 5);
    }

    calc_max_mp() {
        return this.state.level + (this.state.base_dice["WLP"] * 5);
    }

    calc_num_upgrades() {
        return Math.floor(this.state.level/20);
    }
}