export const subjectColors = {
    red: { hex: '#FF6B6B', name: 'Rot' },
    teal: { hex: '#4ECDC4', name: 'Türkis' },
    blue: { hex: '#45B7D1', name: 'Blau' },
    green: { hex: '#96CEB4', name: 'Grün' },
    brown: { hex: '#D4A373', name: 'Braun' },
    purple: { hex: '#9B5DE5', name: 'Lila' },
    pink: { hex: '#F15BB5', name: 'Pink' },
    yellow: { hex: '#FEE440', name: 'Gelb' },
    lightBlue: { hex: '#00BBF9', name: 'Hellblau' },
    mint: { hex: '#00F5D4', name: 'Mint' }
};

export function generateColorPalette() {
    return Object.entries(subjectColors)
        .map(([key, color]) => ({
            id: key,
            hex: color.hex,
            name: color.name
        }));
}
