/**
 * Matches a rule to a block name, with the following cases being possible:
 *
 * 1. ['*'] - matches all blocks
 * 2. '*' can be located somewhere else alongside a string, e.g. 'core/*' - matches all core blocks
 * 3. ['core/paragraph'] - matches only the core/paragraph block
 *
 * @param {*} blockName
 * @param {*} rule
 * @returns True if the block name matches the rule, or false otherwise
 */
export function doesBlockNameMatchBlockRegex( blockName, rule ) {
	if ( rule === '*' ) {
		return true;
	} else if ( rule.includes( '*' ) ) {
		const [ stringToMatch ] = rule.split( '*' );
		return blockName.startsWith( stringToMatch );
	}

	return rule === blockName;
}
