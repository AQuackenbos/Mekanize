Data Schemas
========

## Type



## Meks

	mek:
	{
		id: 0, //player mek id
		body: 0, //Body ID
		hdd: 0, //player HDD id
		item: 0, //player inventory item id
	}

## Bodies

	body:
	{
		id: 0, //ID: unique numerical id of the body type
		core: [{
			id: 0, //Core type ID
		}], 
		parts: //Parts array required
		[{
			id: 0, //Item type ID
			qty: 0, //Qty required or given
		},],
		stats: //base stat data
		{
			hull: 0,
			force: 0,
			armor: 0,
			systems: 0,
			circuitry: 0,
			maneuver: 0,
			agility: 0,
		},
	}

## Items

	item:
	{
		id: 0, //ID: unique numerical id of the item type
		name: "Item Name", //Frontend name for item
		requires: //optional as well as each subfield
		{
			level: 0, //Level of HDD required to use item
			ability: 0, //ID of ability required to use item
			body: 0, //ID of body type required to use item
		}
	}

### Cores
Cores extend off the basic item schema with more information.  They should never have any requirements.  

	core: 
	{
		vs:
		{
			0: 1.0//opposing type ID:received damage multiplier
		},
		terrain:
		{
			0: //terrain type ID
			{
				power: 1.5 //stat: multiplier
			},
		},
	}
	
### Player Item

	player_item:
	{
		id: 0, //player item id
		item: 0 //item type id
	}
	
## Harddrives

	hdd:
	{
		id: 0, //player hdd id
		body: 0, //required body type ID (set when hdd created)
		programming: 
		{
			0: 0, //id of program: levels invested
		}
	}

## Programming

    program:
	{
		id: 0, //program id
		
	}
	
### Moves
	
	move:
	{
		id: 0, //move id
		core: 0, //core type alignment
	}